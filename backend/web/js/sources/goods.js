var Goods = {
    skus:{},
    skuDetails:{},
    getSkus:function() {
        this.skus = {};
        var that = this;
        $('#sku_editor_list').find('.goods-form-sku').each(function(){
            var skuName = $(this).find('.input-sku-name').val();
            if(skuName) {
                that.skus[skuName] = [];
                $(this).find('.sku-option-label').each(function(){
                    that.skus[skuName].push(skuName+':'+$(this).html());
                });
            }
        });
        return this.skus;
    },
    getSkuDetails:function() {
        this.skuDetails = {};
        var containerEle = $('#sku_detail_list');
        var skuDetailEles = containerEle.find('.sku-detail-row');
        var that = this;
        skuDetailEles.each(function(){
            var skuDetailOptions = [];
            var optionEles = $(this).find('.sku-option-label');
            optionEles.each(function(){
                skuDetailOptions.push($(this).html());
            });
            var skuDetailOptionsUnion = skuDetailOptions.join("\t");
            that.skuDetails[skuDetailOptionsUnion] = {
                    options:skuDetailOptions,
                    optionsUnion:skuDetailOptionsUnion,
                    fee:$(this).find('.input-fee').val(),
                    originalFee:$(this).find('.input-original-fee').val(),
                    stock:$(this).find('.input-stock').val(),
                    no:$(this).find('.input-no').val(),
                    sales:$(this).find('.input-sales').val()
                };
        });
        return this.skuDetails;
    },
    renderSkuDetailForm:function() {
        var oldSkuDetails = this.getSkuDetails;
        var newSKuDetails = {};
        var skuDescartes = descartes(this.getSkus());
        var skuDetailsHtml = '';
        for (var k in skuDescartes) {
            var skuDetailOptions = skuDescartes[k];
            var skuDetailOptionsUnion = skuDetailOptions.join("\t");
            if(oldSkuDetails[skuDetailOptionsUnion]) {
                newSKuDetails[skuDetailOptionsUnion] = {
                    options:skuDetailOptions,
                    optionsUnion:skuDetailOptionsUnion,
                    fee:oldSkuDetails[skuDetailOptionsUnion].fee,
                    originalFee:oldSkuDetails[skuDetailOptionsUnion].originalFee,
                    stock:oldSkuDetails[skuDetailOptionsUnion].stock,
                    no:oldSkuDetails[skuDetailOptionsUnion].no,
                    sales:oldSkuDetails[skuDetailOptionsUnion].sales
                };
            } else {
                newSKuDetails[skuDetailOptionsUnion] = {
                    options:skuDetailOptions,
                    optionsUnion:skuDetailOptionsUnion,
                    fee:null,
                    originalFee:null,
                    stock:null,
                    no:null,
                    sales:null
                };
            }
            var html = $('#tpl_edit_sku_detail').html();
            var ele = $(html);
            var optionsHtml = '';
            for(var i in skuDetailOptions) {
                optionsHtml += '<div class="sku-option-label label label-info mr10">'+skuDetailOptions[i]+'</div>';
            }
            if(optionsHtml.length>0) {
                ele.find('.use-sku-options').html(optionsHtml);
            } 
            if(newSKuDetails.price) {
                ele.find('.sku-detail-price').val(newSKuDetails.price);
            }
            if(newSKuDetails.stock) {
                ele.find('.sku-detail-stock').val(newSKuDetails.stock);
            }
            if(newSKuDetails.no) {
                ele.find('.sku-detail-no').val(newSKuDetails.no);
            }
            if(newSKuDetails.sales) {
                ele.find('.sku-detail-sales').val(newSKuDetails.sales);
            }
            skuDetailsHtml += ele.prop("outerHTML");
        }
        if(!skuDetailsHtml) {
            skuDetailsHtml = $('#tpl_edit_sku_detail').html();
        }
        $('#sku_detail_list').html(skuDetailsHtml);
        this.skuDetails = newSKuDetails;
    }
};

$(function(){
    $('#sku_editor_list .label-info').popover({
        content:$('#tpl_edit_sku_option').html(),
        placement:'bottom',
        html:true,
        trigger:'manual'
    });
    
    $('#goods_form').on('click', '.btn-add-sku', function(e) {//添加sku按钮
        e.preventDefault();
        $('#sku_editor_list').append($('#tpl_goods_sku_editor').html());
    }).on('click', '.btn-del-sku', function(e){//删除sku
        e.preventDefault();
        $(this).parents('.goods-form-sku').remove();
    }).on('blur', '.input-sku-name', function(e) {//sku名称 失去焦点
        e.preventDefault();
        $(this).parents('.form-group').removeClass('has-error').find('.help-block').html('');
    }).on('click','.btn-add-sku-option', function(e) {//添加sku选项
        e.preventDefault();
        var parentEle = $(this).parents('.goods-form-sku');
        var inputEle = parentEle.find('.input-sku-option');
        var nameEle = parentEle.find('.input-sku-name');
        if(!nameEle.val()) {
            nameEle.parents('.form-group').addClass('has-error').find('.help-block').html('请先填写名称');
            return false;
        }
        if(inputEle.val()) {
            parentEle.find('.label-list').append('<div class="sku-option-label label label-info">'+inputEle.val()+'</div>')
                    .find('.label').last().popover({
                        content:$('#tpl_edit_sku_option').html(),
                        placement:'bottom',
                        html:true,
                        trigger:'manual'
                    });
            inputEle.val('');
            parentEle.find('.exist-option-list').removeClass('hidden');
            Goods.renderSkuDetailForm();
        }
    }).on('click','.label-info', function(e){//点击sku 属性
        e.preventDefault();
        $(this).popover('toggle');
    }).on('shown.bs.popover', '.label-info', function(e) {//浮层显示
        $(this).next().find('.input-sku-option').val($(this).html());
    }).on('click', '.popover .btn-close', function(e) {
        e.preventDefault();
        $(this).parents('.popover').prev().popover('hide');
    }).on('click', '.popover .btn-del-sku-option', function(e) {//删除sku属性
        e.preventDefault();
        var ele = $(this).parents('.popover').prev();
        var parentEle = ele.parents('.label-list');
        ele.popover('destroy');
        ele.remove();
        if(parentEle.find('.label').length<1) {
            parentEle.parents('.exist-option-list').addClass('hidden');
        }
    }).on('click', '.popover .btn-save-sku-option', function(e) {//更新sku属性
        e.preventDefault();
        var parentEle = $(this).parents('.popover');
        var labelEle = parentEle.prev();
        var inputEle = parentEle.find('.input-sku-option');
        var inputVal = inputEle.val();
        if(!inputVal) {
            FlashMsg.error('选项名称不能为空');
            return false;
        }
        labelEle.html(inputVal).popover('hide');
    }).on('click', '.btn-add-other-fee' , function(e) {//编辑其他费用
        e.preventDefault();
        $('#other_fee_list').append($('#tpl_edit_other_fee').html());
    }).on('click', '.btn-del-other-fee', function(e) {
        e.preventDefault();
        $(this).parents('.edit-other-fee').remove();
    }).on('click', '.btn-add-other-message' , function(e) {//编辑其他费用
        e.preventDefault();
        $('#other_message_list').append($('#tpl_edit_other_message').html());
    }).on('click', '.btn-del-other-message', function(e) {
        e.preventDefault();
        $(this).parents('.edit-other-message').remove();
    });
});