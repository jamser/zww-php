/**
 * 闪屏信息提示
 * FlashMsg.error('内容不能为空');
 * FlashMsg.success('保存成功');
 **/
var FlashMsg = {
    template:'<div class="alert" id="flash_msg" role="alert" style="position:fixed;width:60%;max-width:600px;top:100px;z-index:999;left:0;right:0;margin:0 auto;"></div>',
    typeCssClass:{
        info:'alert-info',
        error:'alert-danger',
        warning:'alert-warning',
        success:'alert-success'
    },
    error:function(msg, time) {
        !time && (time = 3000);
        this.render(msg, 'error', time);
    },
    info:function(msg, time) {
        !time && (time = 2000);
        this.render(msg, 'info', time);
    },
    success:function(msg, time) {
        !time && (time = 2000);
        this.render(msg, 'success', time);
    },
    warning:function(msg, time) {
        !time && (time = 3000);
        this.render(msg, 'warning', time);
    },
    render:function(msg, type, time) {
        var eleId = $(this.template).attr('id');
        if(!eleId) {
            eleId = "flash_msg_" + new Date().getTime();
        }
        
        if($('#'+eleId).length<1)  {
            $(this.template).attr('id', eleId).appendTo('body');
        }
        
        var ele = $('#'+eleId);
        
        for(var t in this.typeCssClass) {
            if(t!==type) {
                ele.removeClass(this.typeCssClass[t]);
            }
        }
        if(ele.timeoutEvents) {
            for(var ek in ele.timeoutEvents) {
                clearTimeout(ele.timeoutEvents[ek]);
                delete ele.timeoutEvents[ek];
            }
        } else {
            ele.timeoutEvents = [];
        }
        ele.html(msg).addClass(this.typeCssClass[type]).fadeIn();
        ele.timeoutEvents.push(setTimeout(function(){
            ele.fadeOut();
        }, time));
    },
    setTemplate:function(template) {
        this.template = template;
        return this;
    },
    setTypeCssClass:function(type,cssClass) {
        typeCssClass[type] = cssClass;
        return this;
    }
    
};
