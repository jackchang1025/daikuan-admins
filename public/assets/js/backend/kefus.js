define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'kefus/index' + location.search,
                    add_url: 'kefus/add',
                    edit_url: 'kefus/edit',
                    del_url: 'kefus/del',
                    multi_url: 'kefus/multi',
                    import_url: 'kefus/import',
                    table: 'kefu',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'kefu_code', title: __('Kefu_code'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'kefu_name', title: __('Kefu_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'kefu_avatar', title: __('Kefu_avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'max_service_num', title: __('Max_service_num')},
                        {field: 'kefu_status', title: __('Kefu_status'), searchList: {"0":__('Kefu_status 0'),"1":__('Kefu_status 1')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
