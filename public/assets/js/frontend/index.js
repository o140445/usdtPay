define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Form.api.bindevent($("#add-form"), function (data, ret) {

                // 跳转
                window.location.href = ret.url;
            }, function (data, ret) {
                Layer.alert(ret.msg, {icon: 2});
            });



        },

        pay: function () {
            // copy
            $("#copy").on("click", function () {
                var clipboard = new ClipboardJS('.btn');

                // 复制成功时的提示
                clipboard.on('success', function (e) {
                    //test-msg-dark
                    Layer.msg(__('Copy success'));
                });
                // 复制失败时的提示
                clipboard.on('error', function (e) {
                });
            });
        }
    };
    return Controller;
});