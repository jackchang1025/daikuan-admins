define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    import_url: 'user/user/import',
                    table: 'user',
                }
            });
            
            var is_status = $("#show").val();
            console.log(is_status)
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true,
                            visible:is_status
                        },
                        {field: 'username', title: __('姓名'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'dk_status', title: __('贷款状态'), formatter: Table.api.formatter.status, searchList: {0: __('暂无贷款'),1: __('已还款'), 2: __('贷款中'), 3: __('结清证明')}},
                        {field: 'jq_image', title: __('公章'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'admin_id', title: __('所属管理员')},
                        {field: 'createtime', title: __('注册时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'dai',
                                    text: __('贷款'),
                                    icon: 'fa fa-list',
                                    classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                    url: 'dai/edit?ids={dai_id}',
                                    callback:function(data){
                                        $('#table').bootstrapTable('refresh');
                                    }
                                },
                                {
                                    name: 'shou',
                                    text: __('收款信息'),
                                    icon: 'fa fa-list',
                                    classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                    url: 'shou/edit?ids={shou_id}',
                                    callback:function(data){
                                        $('#table').bootstrapTable('refresh');
                                    }
                                },
                                {
                                    name: 'edit',
                                    text: __('编辑'),
                                    icon: 'fa fa-edit',
                                    classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                    url: 'user/user/edit?ids={ids}',
                                    callback:function(data){
                                        $('#table').bootstrapTable('refresh');
                                    }
                                },
                                {
                                    name: 'del',
                                    text: __('删除'),
                                    icon: 'fa fa-delete',
                                    classname: 'btn btn-xs btn-success btn-ajax',
                                    confirm:function (row) {
                                        return '是否确定删除该“'+row.username+'”客户？';
                                    },
                                    success:function (data,ret) {
                                        $('#table').bootstrapTable('refresh');
                                    },
                                    url: 'user/user/del?ids={ids}'
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });
            var submitForm = function (ids, layero) {
                var options = table.bootstrapTable('getOptions');
                console.log(options);
                var columns = [];
                $.each(options.columns[0], function (i, j) {
                    if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                        columns.push(j.field);
                    }
                });
                var search = options.queryParams({});
                $("input[name=search]", layero).val(options.searchText);
                $("input[name=ids]", layero).val(ids);
                $("input[name=filter]", layero).val(search.filter);
                $("input[name=op]", layero).val(search.op);
                $("input[name=columns]", layero).val(columns.join(','));
                $("form", layero).submit();
            };
            $(document).on("click", ".btn-export", function () {
                var ids = Table.api.selectedids(table);
                var page = table.bootstrapTable('getData');
                var all = table.bootstrapTable('getOptions').totalRows;
                console.log(ids, page, all);
                Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("user/user/export?ids="+ids) + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                    title: '导出数据',
                    btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                    success: function (layero, index) {
                        $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                    }
                    , yes: function (index, layero) {
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn2: function (index, layero) {
                        var ids = [];
                        $.each(page, function (i, j) {
                            ids.push(j.id);
                        });
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn3: function (index, layero) {
                        submitForm("all", layero);
                        return false;
                    }
                })
            });
            
            $(document).on("click", ".btn-qingkong", function () {
                Layer.confirm("确认清除所有用户数据", {
                    title: '清除数据',
                    btn: ["确定", "取消"],
                    yes: function (index, layero) {
                        $.ajax({
                            url: 'user/user/qingkong', // 你的 API 接口地址
                            type: 'POST', // 请求方式
                            data: { /* 你的数据 */ },
                            success: function(response) {
                                // 请求成功的回调
                                $('#table').bootstrapTable('refresh');
                                layer.msg('清除成功', {icon: 1}); // 使用 layer 显示成功消息
                            },
                            error: function(xhr, status, error) {
                                // 请求失败的回调
                                layer.msg('清除失败', {icon: 2}); // 使用 layer 显示失败消息
                            }
                        });
                       
                    }
                })
            });
            
            $(document).on("change", ".ceshi", function () {
                var dkstatus = $("#dkstatus").val();
                $('#table').bootstrapTable('refreshOptions', {
                    url: 'user/user/index?dkstatus='+dkstatus,
                });
            });
            $(document).on("change", ".adminid", function () {
                var adminid = $("#adminid").val();
                $('#table').bootstrapTable('refreshOptions', {
                    url: 'user/user/index?adminid='+adminid,
                });
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