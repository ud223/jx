function ImageOpt(file, img_id, fun, loaded) {
    // 参数，最大高度
    var MAX_HEIGHT = 1000;
    var path = URL.createObjectURL(file);
    // 创建一个 Image 对象
    var image = document.getElementById(img_id);

    // 绑定 load 事件处理器，加载完成后执行
    image.onload = function () {
        if (MAX_HEIGHT < this.height) {
            var scale = MAX_HEIGHT / this.height;
            var width = this.width;
            var height = this.height;

            this.width = width * scale;
            this.height = height * scale;
        }

        if (loaded) {
            loaded();
        }
    };

    image.src = path;

    if (fun) {
        fun(img_id);
    }
}

function ImageOptByEle(file, img, fun, loaded, code_text) {
    // 参数，最大高度
    var MAX_HEIGHT = 1000;
    var path = URL.createObjectURL(file);
    // 创建一个 Image 对象
    var image = img;
    // 绑定 load 事件处理器，加载完成后执行
    image.load(function () {
        if (MAX_HEIGHT < this.height) {
            var scale = MAX_HEIGHT / this.height;
            var width = this.width;
            var height = this.height;

            this.width = width * scale;
            this.height = height * scale;
        }

        if (loaded) {
            loaded(image, code_text);
        }
    });

    image.prop('src', path);

    if (fun) {
        fun(img);
    }
}

function loadImage(path, img_id) {
    // 参数，最大高度
    var MAX_HEIGHT = 120;
    // 创建一个 Image 对象
    var image = document.getElementById(img_id);
    // 绑定 load 事件处理器，加载完成后执行
    image.onload = function () {
        if (MAX_HEIGHT < this.height) {
            var scale = MAX_HEIGHT / this.height;
            var width = this.width;
            var height = this.height;

            this.width = width * scale;
            this.height = height * scale;
        }
    };

    image.src = path;
}

function convertImgToBase64(url, callback, outputFormat) {
    var canvas = document.createElement('CANVAS'),
        ctx = canvas.getContext('2d'),
        img = new Image;
    img.crossOrigin = 'Anonymous';
    img.onload = function () {
        canvas.height = img.height;
        canvas.width = img.width;
        ctx.drawImage(img, 0, 0);
        var dataURL = canvas.toDataURL(outputFormat || 'image/jpg');

        callback.call(this, dataURL);
        canvas = null;
    };
    img.src = url;
}

function saveImage(url, path, type, text, fun) {
    var data = { 'url': url, 'path': path, 'type': type, 'text': text }
    // alert(JSON.stringify(data));
    fit.ajax({
        url:'/manage/save_photo',
        data:data,
        success:function(result){
            alert(result.msg);

            if (result.code == 200) {
                if (fun) {
                    fun(result.data);
                }
            }
        }
    });
}