app.service('flib', function(){
    return {
        eject: function(arr, el){
            var newArr = [];
            for(var i=0;i<arr.length;++i){
                if (arr[i] == el) continue;
                newArr.push(arr[i]);
            }
            return newArr;
        },
        getSQLDate: function(t){
            return t.getFullYear() + '-' + (t.getMonth() + 1) + '-' + t.getDate();
        },
        findId: function(arr, v, f){
            for(var i=0;i<arr.length;++i){
                if (arr[i].f == v) return arr[i];
            }
            return null;
        }
    }
});