(function(root, definition) {
    if (typeof define === 'function' && define.amd) {
        define([], definition);
    } else {
        root.NotifyMe = definition();
    }
})(this, function() {
    var defaultOptions = {
        icon: 'https://i.imgur.com/97XSERg.jpg',
        lang: 'en-US',
        onclick: function() {},
        onclose: function() {},
        onerror: function() {}
    };

    function extend(base, extension) {
        var extended = {};
        for (var key in base) {
            if (base.hasOwnProperty(key)) {
                extended[key] = base[key];
            }
        }
        for (var key in extension) {
            if (extension.hasOwnProperty(key)) {
                if (extension[key].constructor === Object) {
                    extended[key] = extend(base[key], extension[key]);
                } else {
                    extended[key] = extension[key];
                }
            }
        }
        return extended;
    }

    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16).substring(1);
    }

    function generateTag() {
        return [s4() + s4(), s4(), s4(), s4(), s4() + s4() + s4()].join('-');
    }

    function NotifyMe(title, options) {
        if (!(this instanceof NotifyMe)) {
            return new NotifyMe(title, options);
        }
        this.title = title;
        this.options = extend(defaultOptions, options || {});
        this.options.tag = generateTag();
    }

    NotifyMe.permissionGranted = false;

    NotifyMe.requestPermission = function() {
        return new Promise(function(resolve, reject) {
            Notification.requestPermission(function(permission) {
                NotifyMe.permissionGranted = permission === 'granted';
                if (NotifyMe.permissionGranted) {
                    resolve();
                } else {
                    reject();
                }
            });
        });
    };

    NotifyMe.prototype.launch = function() {
        if (NotifyMe.permissionGranted) {
            this.notification = new Notification(this.title, this.options);
            this.notification.onclick = this.options.onclick;
            this.notification.onerror = this.options.onerror;
            this.notification.onclose = this.options.onclose;
            return Promise.resolve(this.notification);
        } else {
            return NotifyMe.requestPermission()
                .then(this.launch.bind(this))
                .catch(console.warn.bind(console));
        }
    };


    return NotifyMe;
});
