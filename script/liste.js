 document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.trim();
            const url = new URL(window.location.href);
            
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            
            // Mettre à jour l'URL sans recharger la page (pour UX)
            window.history.pushState({}, '', url);
            
            // Soumettre le formulaire de recherche après un délai
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                window.location.href = url.toString();
            }, 500);
        });
        
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.employees-table tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
            });
        });