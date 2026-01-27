const app_config = {
    api_url: "http://localhost/torneos/api/",
    token: localStorage.getItem('gc_token'),
    user: JSON.parse(localStorage.getItem('gc_user')),
    datatables_lang: {
        "url": "assets/js/datatables-es.json"
    }
};

function logout() {
    localStorage.removeItem('gc_token');
    localStorage.removeItem('gc_user');
    window.location.href = "login.php";
}
