window.onload = () => {
    const articles = document.getElementById('articles');

    if (articles) {
        articles.addEventListener('click', e => {
            if (e.target.className === 'btn btn-danger delete-article') {
                if (confirm('Are you sure you want to delete this article? This cannot be undone.')) {
                    const id = e.target.getAttribute('data-id');
                    
                    fetch(`/article/delete/${id}`, {
                        method: 'DELETE'
                    }).then(res => window.location.reload());
                }
            }
        });
    }
  }