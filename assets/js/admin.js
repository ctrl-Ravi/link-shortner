// Admin panel JavaScript functions

// Copy to clipboard function with fallback
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('success', 'Copied!', 'Link copied to clipboard');
        }).catch(() => {
            showNotification('error', 'Error', 'Failed to copy link');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('success', 'Copied!', 'Link copied to clipboard');
        } catch (err) {
            showNotification('error', 'Error', 'Failed to copy link');
        }
        document.body.removeChild(textArea);
    }
}

// Show notification using SweetAlert2
function showNotification(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        alert(text);
    }
}

// Delete confirmation with error handling
function confirmDelete(type, id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `admin.php?action=delete_${type}&id=${id}`;
            }
        });
    } else {
        if (confirm("Are you sure you want to delete this item?")) {
            window.location.href = `admin.php?action=delete_${type}&id=${id}`;
        }
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}); 