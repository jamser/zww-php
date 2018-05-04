var WechatMpMenu = {
    i:1,
    menus: {},
    selectedMenu:null,
    allowEventKeys : {//允许的事件key
    },
    options:{},
    mergeMenuEvents:{},
    mergeSubMenuEvents:{},
    defaultOptions:{
        dialogTemplate:'<div class="modal fade" id="wm_edit_modal" tabindex="-1" role="dialog" aria-labelledby="wm_edit_title" data-backdrop="static"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="关闭"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="wm_edit_title">修改菜单</h4></div><div class="modal-body"><div class="form-inline wm-form" id="wm_form"><div class="form-group"><label for="buttonText" class="form-label">一级菜单</label><input type="text" name="buttonText" class="form-control" placeholder="按钮文字"></div><div class="form-group"><label for="buttonAction" class="sr-only">点击事件</label><select name="buttonAction" class="form-control" id="wm_trgger_event"></select></div><div class="form-group hidden" id="wm_btn_param"></div></div><div class="form-inline hidden" id="wm_sub_form"></div></div><div class="modal-footer"><button type="button" class="btn btn-primary" id="btn_save_wm">保 存</button> <button type="button" class="btn btn-info hidden" id="btn_add_wm_sub_button">添加子菜单按钮</button> <button type="button" class="btn btn-danger" id="btn_remove_wm" data-dismiss="modal">删除该菜单</button> <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button></div></div></div></div>',
        menuBaseEvents:{//菜单基础事件
            click:{
                type:'click',
                name:'点击推事件'
            },
            view:{
                type:'view',
                name:'跳转URL'
            },
            scancode_push:{
                type:'scancode_push',
                name:'扫码推事件'
            },
            scancode_waitmsg:{
                type:'scancode_waitmsg',
                name:'扫码推事件且弹出“消息接收中”提示框'
            },
            pic_sysphoto:{
                type:'pic_sysphoto',
                name:'弹出系统拍照发图'
            },
            pic_photo_or_album:{
                type:'pic_photo_or_album',
                name:'弹出拍照或者相册发图'
            },
            pic_weixin:{
                type:'pic_weixin',
                name:'弹出微信相册发图器'
            },
            location_select:{
                type:'location_select',
                name:'弹出地理位置选择器'
            },
            media_id:{
                type:'media_id',
                name:'下发消息（除文本消息）'
            },
            view_limited:{
                type:'view_limited',
                name:'跳转图文消息URL'
            }
        },
        subMenuEvents : {//子菜单独有事件
        },
        menuEvents : {//一级菜单独有事件
            open_sub_menu:{
                type:'open_sub_menu',
                name:'打开子菜单'
            }
        }
    },
    //保存菜单回调函数
    submitMenuCallback:function(menus) {
            
    },
    //是否正在保存菜单
    submitting:false,
    errorMsg:null,
    //初始化
    init:function(menus, allowEventKeys, submitMenuCallback, options) {
        this.allowEventKeys = allowEventKeys;
        this.options = $.extend({}, this.defaultOptions , options);
        
        $('body').append(this.options.dialogTemplate);
        
        this.mergeMenuEvents = $.extend({}, this.options.menuBaseEvents , this.options.menuEvents);
        this.mergeSubMenuEvents = $.extend({}, this.options.menuBaseEvents , this.options.subMenuEvents);
        
        var menuEventOptionHtml = '<option value="none">选择点击触发的事件</option>';
        for(var k in this.mergeMenuEvents) {
            menuEventOptionHtml += '<option value="'+this.mergeMenuEvents[k].type+'">'+this.mergeMenuEvents[k].name+'</option>';
        }
        this.options.menuEventOptionHtml = menuEventOptionHtml;
        $('#wm_trgger_event').html(menuEventOptionHtml);
        
        var subMenuEventOptionHtml = '<option value="none">选择子菜单按钮点击触发的操作</option>';
        for(var k in this.mergeSubMenuEvents) {
            subMenuEventOptionHtml += '<option value="'+this.mergeSubMenuEvents[k].type+'">'+this.mergeSubMenuEvents[k].name+'</option>';
        }
        this.options.subMenuEventOptionHtml = subMenuEventOptionHtml;
        
        $('select.wm-sub-trigger-event').html(subMenuEventOptionHtml);
        
        if(menus.length>0) {
            for(var k in menus) {
                this.addMenu(menus[k]);
            }
        } else {
            this.addMenu();
        }
        
        this.submitMenuCallback = submitMenuCallback;
        
        this.bindEvents();
    },
    bindEvents:function() {
        //点击添加一级菜单
        var that = this;
        $('#btn_add_wm').on('click',function(e){
            e.preventDefault();
            that.addMenu();
        });

        //删除一级菜单
        $('#btn_remove_wm').on('click',function(e){
            e.preventDefault();
            that.delMenu();
        });
        
        //点击添加子菜单
        $('#btn_add_wm_sub_button').on('click', function(e) {
            e.preventDefault();
            that.addSubMenu();
        });

        //点击删除子菜单
        $('#wm_edit_modal').on('click', '.btn-remove-sm-button', function(e){
            e.preventDefault();
            that.delSubMenu($(this));
        });
        
        //点击按钮 编辑
        $('#wechat_menus').on('click','.wechat-menu', function(e){
            e.preventDefault();
            that.selectedMenu =  $(this);
            that.showEditMenuDialog();
        }).on('mouseenter','.wechat-menu', function(e){//移入的时候显示子菜单
            $(this).find('.wm-sub').removeClass('hidden');
        }).on('mouseout','.wechat-menu', function(e){//移出的时候隐藏子期间
            $(this).find('.wm-sub').addClass('hidden');
        });
        
        //选择一级菜单事件 
        $('#wm_trgger_event').on('change',function(e){
            that.onChangeEvent($(this).val());
        });
        
        //点击菜单编辑的保存事件
        $('#btn_save_wm').on('click',function(e){
            that.saveMenu();
        });
        
        //选择子菜单事件
        $('#wm_edit_modal').on('change','select.wm-sub-trigger-event',function(e){
            that.onChangeSubMenuEvent($(this));
        });
        
        //保存菜单事件
        $('#btn_submit_wm').on('click',function(e){
            e.preventDefault();
            that.submitMenu();
        });
    },
    //产生一个随机数
    randNum:function(min, max) {
                var dis = max-min;
                return min + Math.round(Math.random()*dis);
    },
    //格式化一级菜单
    formatMenu:function(menuData) {
        var formatMenu = {};
        switch(menuData.event) {
            case 'open_sub_menu':
                var subButtons = [];
                for(k in menuData.eventValue) {
                    var subMenuData = menuData.eventValue[k];
                    subButtons.push(this.formatMenu(subMenuData));
                }
                formatMenu = {
                    name:menuData.text,
                    sub_button:subButtons
                };
                break;
            case 'view':
                formatMenu = {
                    type:menuData.event,
                    name:menuData.text,
                    url:menuData.eventValue
                };
                break;
            case 'click'://点击
            case 'pic_sysphoto'://弹出系统拍照发图 
            case 'pic_photo_or_album'://弹出拍照或者相册发图
            case 'pic_weixin'://弹出微信相册发图器
            case 'location_select'://弹出地理位置选择器
            case 'scancode_push':
            case 'scancode_waitmsg':
                formatMenu = {
                    type:menuData.event,
                    name:menuData.text,
                    key:menuData.eventValue
                };
                break;
            case 'media_id':
            case 'view_limited':
                formatMenu = {
                    type:menuData.event,
                    name:menuData.text,
                    media_id:menuData.eventValue
                };
                break;
        }
        return formatMenu;
    },
    //获取一级菜单数量
    getMenuCount:function() {
        return $('#wechat_menus .wechat-menu').length;
    },
    //添加一级菜单
    addMenu:function(menu) {
        var wmCount = this.getMenuCount();
        if(wmCount>=3) {
            alert('一级菜单最多不能超过3个!请先删除后再添加');
            return false;
        }
        wmCount++;
        
        if(!menu) {
            menu = {
                name:null,
                type:null
            };
        }
        this.i++;
        this.renderMenu(menu, this.i);
        $('#wm_sub_form').html('');
    },
    //渲染菜单
    renderMenu:function(menu, n) {
        var subMenuHtml = '';
        if(menu.sub_button) {
            var subMenuHtml = '<ul>';
            for(var k in menu.sub_button) {
                subMenuHtml += '<li><div class="wm-sub-button">'+menu.sub_button[k].name+'</div></li>';
            }
            subMenuHtml+='</ul>';
        }
        var subMenuHeight = (menu.sub_button ? menu.sub_button.length : 0)*40;
        $('#wechat_menus').append('<div class="wechat-menu" data-n="'+n+'" id="wechat_menu_'+n+'" style="border-left:1px solid #BCBCBC;"><div class="wm-text">'+(menu.name ? menu.name : '一级菜单 点击修改')+'</div><div class="wm-sub hidden" style="height:'+subMenuHeight+'px;top:-'+subMenuHeight+'px">'+subMenuHtml+'</div></div>');
        $('#wechat_menus .wechat-menu').css('width', (100/this.getMenuCount())+'%');
        $('#wechat_menus .wechat-menu').first().css('border-left','none');
        this.menus[n] = menu;
    },
    //删除一级菜单
    delMenu:function() {
        var wmCount = this.getMenuCount();
        if(wmCount<=1) {
            alert('一级菜单最少1个..因此不能删除该一级菜单');
            return false;
        }
        var n = this.selectedMenu.data('n');
        if(this.menus[n]) {
            delete this.menus[n];
        }
        this.selectedMenu.remove();
        this.selectedMenu = null;
        wmCount--;
        $('#wechat_menus .wechat-menu').css('width',(100/wmCount)+'%').first().css('border-left','none');
    },
    //显示编辑菜单弹出框
    showEditMenuDialog:function() {
        var ele = this.selectedMenu;
        var n = ele.data('n');
        var menu = this.menus[n];
        $('#wm_edit_modal').modal('show');
        $('#wm_form input[name=buttonText]').val(menu.name?menu.name:'');
        var event = menu.type ? menu.type : (menu.sub_button && menu.sub_button.length>0 ? 'open_sub_menu' : 'none');
        $('#wm_trgger_event').val(event);
        this.onChangeEvent(event, menu);
    },
    //保存一级菜单
    saveMenu:function() {
        var buttonText = $('#wm_form input[name=buttonText]').val();//按钮文字
        var buttonEvent = $('#wm_trgger_event').val();//按钮事件
        var menuData = {
            text:buttonText,
            event:buttonEvent,
            eventValue:this.getMenuEventParams(buttonEvent)
        };
        if(this.validateMenuData(menuData)) {//验证通过
            var n = this.selectedMenu.data('n');
            this.selectedMenu.find('.wm-text').html(buttonText);
            $('#wm_form input[name=buttonText]').val('');
            $('#wm_trgger_event').val('none');
            $('#wm_sub_form').html('');
            this.onChangeEvent('none');
            $('#wm_edit_modal').modal('hide');
            var formatData = this.formatMenu(menuData);
            this.menus[n] = formatData;
            if(formatData.sub_button) {
                var subMenuHtml = '<ul>';
                for(var k in formatData.sub_button) {
                    subMenuHtml += '<li><div class="wm-sub-button">'+formatData.sub_button[k].name+'</div></li>';
                }
                subMenuHtml+='</ul>';
                var subMenuHeight = (formatData.sub_button ? formatData.sub_button.length : 0)*40;
                this.selectedMenu.find('.wm-sub').html(subMenuHtml).css({height:subMenuHeight+'px',top:-subMenuHeight+'px'});
            }
        } else {//验证失败
            alert(this.errorMsg);
            return false;
        }
    },
    getMenuEventParams:function(event) {
        var param = null;
        switch(event) {
            case 'open_sub_menu':
                var param = this.getSubMenuData();
                break;
            case 'view':
                var param = $('#wm_btn_param').find('.ep-url').val();
                break;
            case 'click'://点击
            case 'pic_sysphoto'://弹出系统拍照发图 
            case 'pic_photo_or_album'://弹出拍照或者相册发图
            case 'pic_weixin'://弹出微信相册发图器
            case 'location_select'://弹出地理位置选择器
            case 'scancode_push':
            case 'scancode_waitmsg':
                var param = $('#wm_btn_param').find('.ep-type').val();
                break;
            case 'media_id':
            case 'view_limited':
                var param = $('#wm_btn_param').find('.ep-media-id').val();
                break;
        }
        return param;
    },
    //验证菜单数据 
    validateMenuData:function(menuData, isSubMenu) {
        if(!menuData.text || menuData.text.length<=0) {
            this.errorMsg = '菜单文字不能为空';
            return false;
        }
        if(!menuData.event || menuData.event==='none') {
            this.errorMsg = '菜单事件不能为空';
            return false;
        } else if(!(this.mergeMenuEvents[menuData.event])) {
            this.errorMsg = '该菜单事件未被定义 , 因此不能使用';
            return false;
        }
        
        switch(menuData.event) {
            case 'open_sub_menu':
                if(isSubMenu) {
                    this.errorMsg = '子菜单中不能再包含一个子菜单';
                    return false;
                }
                if(menuData.eventValue.length<1 || menuData.eventValue.length>5) {
                    this.errorMsg = '子菜单必须在1-5个之间';
                    return false;
                }
                for(k in menuData.eventValue) {
                    var subMenuData = menuData.eventValue[k];
                    if(this.validateMenuData(subMenuData, 1)) {
                        continue;
                    } else {
                        return false;
                    }
                }
                break;
            case 'view':
                if(menuData.eventValue.toLowerCase().indexOf('http://')!==0 && menuData.eventValue.toLowerCase().indexOf('https://')!==0) {
                    this.errorMsg = '打开链接属性必须以 http:// 或 https:// 作为开始';
                    return false;
                }
                break;
            case 'click'://点击
            case 'pic_sysphoto'://弹出系统拍照发图 
            case 'pic_photo_or_album'://弹出拍照或者相册发图
            case 'pic_weixin'://弹出微信相册发图器
            case 'location_select'://弹出地理位置选择器
            case 'scancode_push':
            case 'scancode_waitmsg':
                if(!menuData.eventValue || menuData.eventValue==='none') {
                    this.errorMsg = (isSubMenu?'子菜单':'菜单')+'点击事件类型不能为空';
                    return false;
                } else if(!this.allowEventKeys[menuData.eventValue]) {
                    this.errorMsg = '该事件类型不在允许范围内 , 因此不能使用';
                    return false;
                }
                break;
            case 'media_id':
            case 'view_limited':
                if(!menuData.eventValue) {
                    this.errorMsg = '媒体ID不能为空';
                    return false;
                }
                break;
        }
        return true;
    },
    //获取当前选择菜单的子菜单数据
    getSubMenuData:function() {
        var subMenuDatas = [];
        $('#wm_sub_form .wm-sub-form').each(function(){
            subMenuDatas.push({
                text:$(this).find('input[name=buttonText]').val(),
                event:$(this).find('select[name=buttonEvent]').val(),
                eventValue:$(this).find('.ep-param').val()
            });
        });
        return subMenuDatas;
    },
    //获取当前选择菜单 包含子菜单的数量
    getSubMenuCount:function() {
        return $('#wm_sub_form .wm-sub-form').length;
    },
    //添加子菜单
    addSubMenu:function(subMenu) {
        var wmSubCount = this.getSubMenuCount();
        if(wmSubCount>=5) {
            $('#btn_add_wm_sub_button').addClass('hidden');
            alert('子菜单已经到上限了 , 请先删除');
            return false;
        }
        var wmSubHtml = '<div class="wm-sub-form clearfix"><div class="form-group"><label for="buttonText" class="form-label">子菜单</label><input type="text" name="buttonText" class="form-control" placeholder="按钮文字" '+(subMenu ? 'value="'+subMenu.name+'"' : '')+'></div><div class="form-group"><label for="buttonEvent" class="sr-only">点击事件</label><select name="buttonEvent" class="form-control wm-sub-trigger-event">'+this.options.subMenuEventOptionHtml+'</select></div><div class="form-group ep-group"></div><div class="form-group"><a href="#" class="btn-remove-sm-button">删除</a></div></div>';
        $('#wm_sub_form').append(wmSubHtml);
        if(subMenu) {
            var ele = $('#wm_sub_form .wm-sub-form').last().find('select.wm-sub-trigger-event');
            ele.val(subMenu.type);
            this.onChangeSubMenuEvent(ele, subMenu);
        }
        if( (++wmSubCount) >=5 ) {
            $('#btn_add_wm_sub_button').addClass('hidden');
        }
    },
    //删除子菜单
    delSubMenu:function(ele) {
        var wmSubCount = this.getSubMenuCount();
        if(wmSubCount<=1) {
            alert('至少需要包括1个子菜单');
            return false;
        } else {
            ele.parents('.wm-sub-form').remove();
            if( (--wmSubCount) <5) {
                $('#btn_add_wm_sub_button').removeClass('hidden');
            }
        }
    },
    //在改变事件的时候触发
    onChangeEvent:function (selectedEvent, menu) {
        $('#wm_sub_form').addClass('hidden');
        $('#wm_btn_param').html('').addClass('hidden');
        $('#btn_add_wm_sub_button').addClass('hidden');
        switch(selectedEvent) {
            case 'open_sub_menu'://打开子菜单 显示子菜单
                $('#wm_sub_form').removeClass('hidden');
                if(menu && menu.sub_button) {
                    $('#wm_sub_form').html('');
                    for(var k in menu.sub_button) {
                        this.addSubMenu(menu.sub_button[k]);
                    }
                }
                if($('#wm_sub_form .wm-sub-form').length<5) {
                    $('#btn_add_wm_sub_button').removeClass('hidden');
                }
                if($('#wm_sub_form .wm-sub-form').length<1) {
                    this.addSubMenu();
                }
                break;
            case 'view'://跳转URL 需要填写URL
                var html = '<input type="url" class="form-control ep-url"  placeholder="请输入跳转的url" value="'+(menu?menu.url:'')+'"/>';
                $('#wm_btn_param').html(html).removeClass('hidden');
                break;
            case 'click'://点击
            case 'pic_sysphoto'://弹出系统拍照发图 
            case 'pic_photo_or_album'://弹出拍照或者相册发图
            case 'pic_weixin'://弹出微信相册发图器
            case 'location_select'://弹出地理位置选择器
            case 'scancode_push':
            case 'scancode_waitmsg':
                var html = '<select class="form-control ep-type"><option>请选择事件值</option>';
                for(k in this.allowEventKeys) {
                    html += '<option value="'+k+'" '+((menu && menu.key===k) ? 'selected' : '')+'>'+this.allowEventKeys[k]+'</option>';
                }
                html += '</select>';
                $('#wm_btn_param').html(html).removeClass('hidden');
                break;
            case 'media_id'://发送多媒体消息 需要填写多媒体ID
            case 'view_limited'://跳转图文消息URL 需要填写多媒体ID
                var html = '<input type="text" class="form-control ep-media-id" placeholder="请输入多媒体ID" value="'+(menu?menu.media_id:'')+'"/>';
                $('#wm_btn_param').html(html).removeClass('hidden');
                break;
            default:
                break;
        }
    },
    //子菜单选择事件
    onChangeSubMenuEvent:function (selectEle, subMenu) {
        selectedEvent = selectEle.val();
        var paramEle = selectEle.parents('.wm-sub-form').find('.ep-group');
        switch(selectedEvent) {
            case 'view'://跳转URL 需要填写URL
                var html = '<input type="url" class="form-control ep-url  ep-param"  placeholder="请输入跳转的url" value="'+(subMenu&&subMenu.url?subMenu.url:'')+'"/>';
                paramEle.html(html).removeClass('hidden');
                break;
            case 'click'://点击
            case 'pic_sysphoto'://弹出系统拍照发图 
            case 'pic_photo_or_album'://弹出拍照或者相册发图
            case 'pic_weixin'://弹出微信相册发图器
            case 'location_select'://弹出地理位置选择器
            case 'scancode_push':
            case 'scancode_waitmsg':
                var html = '<select class="form-control ep-type ep-param"><option value="none">请选择事件值</option>';
                for(var k in this.allowEventKeys) {
                    html += '<option value="'+k+'" '+(subMenu && subMenu.key===k ? 'selected' : '')+'>'+this.allowEventKeys[k]+'</option>';
                }
                html += '</select>';
                paramEle.html(html).removeClass('hidden');
                break;
            case 'media_id'://发送多媒体消息 需要填写多媒体ID
            case 'view_limited'://跳转图文消息URL 需要填写多媒体ID
                var html = '<input type="text" class="form-control ep-media-id  ep-param" placeholder="请输入多媒体ID"  value="'+(subMenu&&subMenu.media_id?subMenu.media_id:'')+'"/>';
                paramEle.html(html).removeClass('hidden');
                break;
            default:
                break;
        }
    },
    //提交菜单
    submitMenu:function() {
        var submitMenus = [];
        for(var k in this.menus) {
            if(this.menus[k].name) {
                submitMenus.push(this.menus[k]);
            }
        }
        this.submitMenuCallback(submitMenus);
    }
};