</div>

<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('هل أنت متأكد من الحذف؟')) e.preventDefault();
    });
});
</script>
</body>
</html>
