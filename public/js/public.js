$(function(){

})
var timestamp = function (){ return Date.parse(new Date()) / 1000 ; };
var _storage;
function loadStorage() {
    if (_storage == false) return false;
    try {
        _storage = window.sessionStorage;
        _storage.setItem('loadTime', timestamp()/1000);
    } catch(err) {
        _storage = false;
    }
}
loadStorage();
function getStorage(name) {
    if (_storage == false) return false;
    return JSON.parse(_storage.getItem(name));
}
function setStorage(name, value) {
    if (_storage == false) return false;
    _storage.setItem(name, JSON.stringify(value));
}
function clearStorage() {
    if (_storage == false) return false;
    _storage.clear();
}
function removeStorage(name) {
    if (_storage == false) return false;
    _storage.removeItem(name);
}
function ajax(urls, datas, functions, cached) {
    if(cached) {
        var data = getStorage(urls);
        if (data) {functions(data);return;}
    }
    $.ajax({
        type     : "POST",
        url      :  urls,
        datatype : 'json',
        data     : datas,
        success  : function(data) {
            var data = JSON.parse(data);
            if(cached) {setStorage(urls, data)};
            functions(data);
        },
    });
}