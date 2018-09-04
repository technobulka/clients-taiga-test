</div>

<script>
    var d = document,
        add_btn = d.querySelector('.add'),
        phones = d.querySelector('.phones'),
        proto = d.querySelector('.proto');

    if (add_btn) {
        // append input for new phone
        add_btn.addEventListener('click', function() {
            var clone = proto.cloneNode(true);

            clone.classList.remove('d-none');
            phones.appendChild(clone);

            return false;
        });

        // delete phone onclick del button
        d.addEventListener('click', function(e) {
            if (e.target && /\bdel\b/.test(e.target.className)) {
                e.target.parentNode.parentNode.remove();
            }
        });
    }
</script>

</body>
</html>