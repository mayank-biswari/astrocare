{{-- PII Reveal Script - Handles AJAX reveal of masked PII fields --}}
<script>
(function() {
    'use strict';

    /**
     * PII Reveal Handler
     * Uses event delegation to handle reveal button clicks.
     * Sends POST to /lms/leads/{id}/reveal-pii with field name.
     * On success: replaces masked text with actual value and hides button.
     * On 403: shows "Access denied" toast.
     * No localStorage — revealed values are lost on page reload.
     */
    document.addEventListener('click', function(event) {
        const btn = event.target.closest('.pii-reveal-btn');
        if (!btn) return;

        const leadId = btn.getAttribute('data-lead-id');
        const field = btn.getAttribute('data-field');

        if (!leadId || !field) return;

        // Prevent double-clicks
        if (btn.disabled) return;
        btn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token meta tag not found.');
            btn.disabled = false;
            return;
        }

        fetch('/lms/leads/' + leadId + '/reveal-pii', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ field: field })
        })
        .then(function(response) {
            if (response.status === 403) {
                showPiiToast('Access Denied', 'You do not have permission to view this information.');
                btn.disabled = false;
                return null;
            }
            if (!response.ok) {
                showPiiToast('Error', 'Failed to reveal PII. Please try again.');
                btn.disabled = false;
                return null;
            }
            return response.json();
        })
        .then(function(data) {
            if (!data) return;

            // Find the matching masked value span
            var span = document.querySelector(
                '.pii-masked-value[data-lead-id="' + leadId + '"][data-field="' + field + '"]'
            );

            if (span) {
                span.textContent = data.value;
                span.classList.remove('pii-masked-value');
                span.classList.add('pii-revealed-value');
            }

            // Hide the reveal button
            btn.style.display = 'none';
        })
        .catch(function(error) {
            console.error('PII reveal error:', error);
            showPiiToast('Error', 'Network error. Please try again.');
            btn.disabled = false;
        });
    });

    /**
     * Show a toast notification for PII reveal feedback.
     * Uses the existing showToast function if available, otherwise falls back to alert.
     */
    function showPiiToast(title, message) {
        if (typeof window.showToast === 'function') {
            window.showToast(title, message);
        } else {
            alert(title + ': ' + message);
        }
    }
})();
</script>
