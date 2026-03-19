# 🏦 Financial Transaction System

## 📌 Overview

This project is a **real-world simulation of a financial transaction processing system**, designed to handle:

- Payments
- Wallet balance updates
- Webhook-based confirmations
- Concurrency & race conditions
- Idempotency & duplicate prevention

The goal is to **demonstrate senior-level backend engineering skills**, focusing on reliability, consistency, and real production challenges.

---

## 🎯 Project Goals

This project is built to prove:

- Ability to design **financial systems**
- Handling **concurrent requests safely**
- Implementing **idempotent APIs**
- Managing **event-driven workflows**
- Ensuring **data consistency under failure**

---

## 🧱 System Architecture

This system uses a modular architecture with DTOs and interface-based service and repository layers.

```bash
Controller -> DTO -> Service Layer (Interface) -> Repository Layer (Interface) -> Database
        ↓
       Queue
        ↓
Webhook Processor
```

---

## ⚙️ Core Features

### 1. Transaction Creation

- Create transaction with `pending` status
- Deduct balance safely

### 2. Idempotency

- Prevent duplicate transactions
- Use `idempotency_key`

### 3. Concurrency Control

- Prevent race conditions
- Use DB transactions + row locking

### 4. Wallet System

- Maintain user balance
- Deduct & refund safely

### 5. Webhook Processing

- Handle async confirmation
- Support success & failure

### 6. Queue System

- Process webhooks asynchronously
- Retry failed jobs

---

## 🗄️ Database Design

### Users

| Field   | Type    |
| ------- | ------- |
| id      | int     |
| name    | string  |
| balance | decimal |

---

### Transactions

| Field           | Type    |
| --------------- | ------- |
| id              | int     |
| user_id         | int     |
| amount          | decimal |
| status          | enum    |
| idempotency_key | string  |
| reference       | string  |

---

## 🔄 System Flows

---

### 🟢 Flow 1: Create Transaction

1. Validate request
2. Check idempotency key
3. Start DB transaction
4. Lock user row (`FOR UPDATE`)
5. Check balance
6. Deduct amount
7. Create transaction (pending)
8. Commit

---

### 🔵 Flow 2: Webhook Processing

1. Receive webhook
2. Validate reference
3. Check if already processed
4. Update transaction:
    - success → finalize
    - failed → refund
5. Save result

---

### 🔴 Flow 3: Retry Handling

- Failed jobs → retry automatically
- Prevent duplicate processing
- Optional exponential backoff

---

## 🧩 Engineering Challenges & Solutions

### 🔴 Race Conditions

**Solution:**

- `DB::transaction`
- `lockForUpdate()`

---

### 🔴 Duplicate Requests

**Solution:**

- Unique `idempotency_key`
- Return existing transaction

---

### 🔴 Webhook Duplication

**Solution:**

- Idempotent webhook handler
- Status check before update

---

### 🔴 System Failures

**Solution:**

- Queue retries
- Rollback logic

---

## 🧠 Implementation Plan (STEP-BY-STEP)

---

### ✅ Phase 1: Basic Setup

- ✅ Create Laravel project
- [ ] Setup database
- [ ] Create models:
- [ ] User
- [ ] Transaction

---

### ✅ Phase 2: Core Logic

- [ ] Create TransactionService
- [ ] Implement:
- [ ] DB transactions
- [ ] Balance deduction
- [ ] Idempotency

---

### ✅ Phase 3: API Layer

- [ ] POST /transactions
- [ ] GET /transactions/{id}

---

### ✅ Phase 4: Concurrency Handling

- [ ] Add `lockForUpdate()`
- [ ] Test simultaneous requests

---

### ✅ Phase 5: Webhook System

- [ ] Create webhook endpoint
- [ ] Create ProcessWebhook job
- [ ] Handle success/failure

---

### ✅ Phase 6: Queue System

- [ ] Configure queue (database/redis)
- [ ] Add retries

---

### ✅ Phase 7: Testing

- [ ] Test idempotency
- [ ] Test balance consistency
- [ ] Test duplicate requests

---

### ✅ Phase 8: Enhancements

- [ ] Add Redis locking
- [ ] Add rate limiting
- [ ] Add logging
- [ ] Add monitoring

---

## 🧪 Example Scenario

### Duplicate Request

User sends 2 identical requests:

→ First request:

- locks balance
- creates transaction

→ Second request:

- finds existing idempotency key
- returns same transaction

✅ No duplicate

---

### Race Condition

Two requests at same time:

→ Only one locks the row  
→ Second waits  
→ Balance remains correct

---

## 📡 API Endpoints

```bash
POST   /api/transactions
GET    /api/transactions/{id}
POST   /api/webhooks/payment
```

---

## 🛠️ Tech Stack

- Laravel
- MySQL
- Laravel Queues
- Redis (optional)

---

## 🚀 How to Run

```bash
git clone https://github.com/Zohair22/financial_transaction_system.git
cd financial_transaction_system

composer install
cp .env.example .env
php artisan key:generate

php artisan migrate

php artisan serve
```

---

## ⚖️ Trade-offs

- DB locking → strong consistency but slower
- Queues → reliable but adds complexity

---

## 🚀 Scalability Plan

- Use Redis for distributed locks
- Horizontal queue workers
- Add rate limiting
- Add caching

---

## 🔮 Future Improvements

- Dead-letter queue
- Audit logs
- Payment provider simulation
- Dashboard

---

## 💡 What This Project Proves

- Real backend engineering skills
- Financial system design
- Concurrency handling
- Production-level thinking

---

## 👨‍💻 Author

Backend Engineer focused on:

- Financial systems
- Backend architecture
- Scalable APIs
