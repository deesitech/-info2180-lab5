document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('lookup').addEventListener('click', function() {
        const country = document.getElementById('country').value.trim();
        let url = 'world.php';
        if (country) {
            url += '?country=' + encodeURIComponent(country);
        }

        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('result').innerHTML = data;
            })
            .catch(() => {
                document.getElementById('result').innerHTML = '<p style="color: red;">Error loading data</p>';
            });
    });

    document.getElementById('lookup-cities').addEventListener('click', function() {
        const country = document.getElementById('country').value.trim();
        if (!country) {
            document.getElementById('result').innerHTML = '<p style="color: red;">Please enter a country name first!</p>';
            return;
        }
        const url = 'world.php?country=' + encodeURIComponent(country) + '&lookup=cities';

        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('result').innerHTML = data;
            })
            .catch(() => {
                document.getElementById('result').innerHTML = '<p style="color: red;">Error loading cities</p>';
            });
    });
});