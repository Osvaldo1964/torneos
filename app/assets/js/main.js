const app_config = {
    api_url: window.location.origin + window.location.pathname.split('app/')[0] + "api/",
    base_url: window.location.origin + window.location.pathname.split('app/')[0],
    token: localStorage.getItem('gc_token'),
    user: JSON.parse(localStorage.getItem('gc_user')),
    datatables_lang: {
        "url": "assets/js/datatables-es.json"
    }
};

function logout() {
    localStorage.removeItem('gc_token');
    localStorage.removeItem('gc_user');
    window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : "") + "login.php";
}
