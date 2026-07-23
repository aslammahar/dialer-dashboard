<div class="details-section">
    <h2>Record #{{ $queueSale->id }} Details</h2>
    <p><strong>Name:</strong> {{ $queueSale->customer_full_name ?? 'N/A' }}</p>
    <p><strong>State:</strong> {{ $queueSale->state ?? 'N/A' }}</p>
    <p><strong>Carrier:</strong> {{ $queueSale->carrier ?? 'N/A' }}</p>
    <p><strong>Client:</strong> {{ $queueSale->client_name ?? 'N/A' }}</p>
    <p><strong>Closer:</strong> {{ $queueSale->closedCall->closername ?? 'N/A' }}</p>
    <p><strong>Validator:</strong> {{ $queueSale->validator ? $queueSale->validator->code . ' - ' . $queueSale->validator->name : 'N/A' }}</p>
    <p><strong>Status:</strong> {{ $queueSale->status }}</p>
    <p><strong>Connect:</strong> {{ $queueSale->connect_status }}</p>
    <!-- Add more fields -->
</div>

<div class="comment-section">
    <h3>Comments</h3>
    <form class="comment-form" onsubmit="addComment(event, {{ $queueSale->id }})">
        <textarea name="content" placeholder="Write a comment..."></textarea>
        <button type="submit">Add Comment</button>
    </form>
    <div class="comments-list">
        {{-- Initial comments loaded via JS --}}
    </div>
</div>

<script>
    // Load initial comments on modal open
    loadComments({{ $queueSale->id }});
</script>