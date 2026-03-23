<button id="connect-bank">Connect Bank</button>
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script>
document.getElementById('connect-bank').addEventListener('click', async () => {
    // 1️⃣ Get link token from backend
    const res = await fetch('/api/plaid/link-token', { headers: { 'Authorization': 'Bearer link-sandbox-592706db-5175-428f-9753-c79320041dc3' } });
    const data = await res.json();

    const handler = Plaid.create({
        token: data.link_token,
        onSuccess: async function(public_token, metadata) {
            // 2️⃣ Exchange public_token for access_token
            await fetch('/api/plaid/exchange-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer link-sandbox-592706db-5175-428f-9753-c79320041dc3'
                },
                body: JSON.stringify({ public_token })
            });
            alert('Bank connected successfully!');
        }
    });

    handler.open();
});
</script>
