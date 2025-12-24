define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dai/index' + location.search,
                    add_url: 'dai/add',
                    edit_url: 'dai/edit',
                    del_url: 'dai/del',
                    multi_url: 'dai/multi',
                    import_url: 'dai/import',
                    table: 'user_dai',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'dkrq', title: __('Dkrq'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'hkrq', title: __('Hkrq'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'yq_money', title: __('Yq_money'), operate:'BETWEEN'},
                        {field: 'yh_money', title: __('Yh_money'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
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
