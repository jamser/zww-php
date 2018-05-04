//regionData.typeIds;
//regionData.allRegions;
//regionData.typeRegionIds;
//regionData.childrenIds;


if(typeof rules==='undefined') {
    var rules = {
//            {
//                id:1,
//                regionIds:[2,5,6],
//                first_amout:0,
//                first_fee:0,
//                addination_amout:0,
//                addination_fee:0
//            }
    };
}

var RegionSelect = {
    showDepthLevel:1,//显示深度  0表示第一层(省份) 1表示第二层(城市)
    regionRuleIds : {},
    editRuleId:null,
    updating:false,
    initData:function() {//初始化地区数据
        //解析所有的区域数据
        regionData = $.parseJSON(regionData);
        
        for (var k in rules) {
            var rule = rules[k];
            var ruleId = rule.id;
            for (var r in rule.regionIds) {
                var regionId = rule.regionIds[r];
                this.regionStatus[regionId] = ruleId;
            }
        }
    },
    resetEditor:function() {
        $('#unselected_depth0').html('');
        $('#selected_depth0').html('');
    },
    renderRuleEditor:function(ruleEle) {//渲染已经规则地区数据
        this.resetEditor();
        var selectedIds = {};
        var that = this;
        ruleEle.find('.selected-item').each(function() {
            var regionName = $(this).html();//地区名称
            var regionId = $(this).data('id');//地区ID
            var region = regionData[regionId];
            var parentIds = that.getTreeIds(regionId);
            var d = 0;
            while(parentIds.length>0) {
                var treeRegionId = parentIds.pop();
                if(!selectedIds[d]) {
                    selectedIds[d] = {};
                }
                selectedIds[d][treeRegionId] = treeRegionId;
                d++;
            }
        });
        this.renderSelectedIds(selectedIds);
        this.renderSelectableIds();
    },
    renderNewRuleEditor:function() {
        this.resetEditor();
        this.renderSelectedIds({});
        this.renderSelectableIds();
    },
    getTreeIds:function(regionId) {
        var parentIds = [regionId];
        while(true) {
            var region = regionData.allRegions[regionId];
            if(!region || !region.pid || region.pid==='0') {
                break;
            }
            parentIds.push(region.pid);
            regionId = region.pid;
        }
        return parentIds;
    },
    renderSelectedIds:function(selectedIds) {
        var that = this;
        for(var depth in selectedIds) {
            var depth = Number(depth);
            if(depth>that.showDepthLevel) {
                break;
            }
            var depthIds = selectedIds[depth];
            for (var k in depthIds) {
                var regionId = depthIds[k];
                var region = regionData.allRegions[regionId];
                var ele = $($('#tpl_depth'+depth).html());
                ele.find('.region-name').html(region.name);
                ele.attr('data-id',regionId).attr('data-depth', depth).attr('id','selected_region_'+regionId);
                if(depth===that.showDepthLevel) {
                    ele.addClass('depth-last');
                    if(region.name==='县' || region.name==='市辖区') {
                        continue;
                    }
                }
                
                if($('#selected_region_'+regionId).length>0) {
                    continue;
                }
                
                if(depth===0) {
                    ele.appendTo($('#selected_depth'+depth));
                } else {
                    var parentRegionId = region.pid;
                    ele.appendTo($('#selected_region_'+parentRegionId).children('.region-children-list'));
                }
            }
        }
    },
    renderSelectableIds:function() {
        var that = this;
        for(var depth in regionData.typeRegionIds) {
            depth = Number(depth);
            if(depth>that.showDepthLevel) {
                break;
            }
            var depthIds = regionData.typeRegionIds[depth];
            for (var k in depthIds) {
                var regionId = depthIds[k];
                var region = regionData.allRegions[regionId];
                var ele = $($('#tpl_depth'+depth).html());
                ele.find('.region-name').html(region.name);
                ele.attr('data-id',regionId).attr('data-depth', depth).attr('id','unselected_region_'+regionId);
                if(depth===that.showDepthLevel) {
                    ele.addClass('depth-last');
                    if(region.name==='县' || region.name==='市辖区') {
                        continue;
                    }
                }
                if(this.regionRuleIds[regionId]) {
                    ele.addClass('hidden');
                }
                if(depth===0) {
                    ele.appendTo($('#unselected_depth'+depth));
                } else {
                    var parentRegionId = region.pid;
                    ele.appendTo($('#unselected_region_'+parentRegionId).children('.region-children-list'));
                }
            }
        }
    },
    addSelectedRegion:function() {
        //从可选地区中移除 并放入到已选队列中
        var that = this;
        var selectedIds = {};
        $('#unselected_depth0').find('.region-title.active').each(function(){
            var itemEle = $(this).closest('.region-item');
            var depth = itemEle.data('depth');
            var regionId = itemEle.data('id');
            var region = regionData[regionId];
            var parentIds = that.getTreeIds(regionId);
            var d = 0;
            while(parentIds.length>0) {
                var treeRegionId = parentIds.pop();
                if(!selectedIds[d]) {
                    selectedIds[d] = {};
                }
                selectedIds[d][treeRegionId] = treeRegionId;
                if(that.allChildrenSelected(treeRegionId)) {
                    $('#unselected_region_'+treeRegionId).children('.region-title').addClass('active');
                }
                d++;
            }
            itemEle.addClass('hidden');
        });
        this.renderSelectedIds(selectedIds);
    },
    saveSelectedRegion:function() {
        var that = this;
        //保存选择的区域到规则中
        var selectedRegionHtml = '';
        $('#selected_depth0').find('.region-item').each(function(){
            var regionId = $(this).data('id');
            var region = regionData.allRegions[regionId];
            if(that.allChildrenSelected(regionId)) {
                selectedRegionHtml += (selectedRegionHtml.length>0 ? '、' : '')+ '<span class="selected-item" data-id="'+regionId+'">'+region.name+'</span>';
            }
        });
        
        //新建的规则
        if(!this.editRuleId) {
            var ele = $($('#tpl_dt_rule').html());
            var time = new Date();
            var ruleId = 'new_'+time.getTime();
            ele.attr('id', 'dt_rule_'+ruleId).attr('data-id', ruleId);
            ele.appendTo($('#dt_rule'));
            this.editRuleId = ruleId;
        }
        
        $('#dt_rule_'+this.editRuleId).find('.selected-region-list').html(selectedRegionHtml);
        
        that.regionRuleIds = {};
        
        $('#dt_rule').find('.selected-item').each(function() {
            var regionId = $(this).data('id');
            var ruleId = $(this).closest('.dt-rule').data('id');
            that.regionRuleIds[regionId] = ruleId;
        });
    },
    allChildrenSelected:function(id) {
        var region = regionData.allRegions[id];
        if(region.type===this.showDepthLevel) {
            return true;
        }
        var childrenIds = regionData.childrenIds[id];
        if(childrenIds && childrenIds.length>0) {
            for(var k in childrenIds) {
                var childId = childrenIds[k];
                var childRegion = regionData.allRegions[childId];
                if(childRegion.type===this.showDepthLevel && (childRegion.name==='县' || childRegion.name==='市辖区') ) {
                    continue;
                }
                if($('#selected_region_'+childId).length===0) {
                    return false;
                }
            }
        }
        return true;
    },
    delSelectedRegion:function(itemEle) {
        //从已选中移除 并重新放入到可选地区中
        var that = this;
        var regionId = itemEle.data('id');
        //移除已经选择的元素
        var treeIds = this.getTreeIds(regionId);
        //恢复父级元素
        while(treeIds.length>0) {
            var treeId = treeIds.pop();
            $('#unselected_region_'+treeId).removeClass('hidden').children('.region-title').removeClass('active');
        }
        
        //恢复所有子元素
        itemEle.children('.region-children-list').find('.region-item').each(function(){
            var childRegionId = $(this).data('id');
            $('#unselected_region_'+childRegionId).removeClass('hidden').children('.region-title').removeClass('active');
        });
        
        //判断当前是否还有已经选择的兄弟元素
        var removeParentItemEle = itemEle.siblings('.region-item').length===0 ? itemEle.parent().closest('.region-item') : null;
        
        //删除对应的元素
        itemEle.remove();
        
        if(removeParentItemEle && removeParentItemEle.length>0) {
            this.delSelectedRegion(removeParentItemEle);
        }
    },
    addParentActive:function(regionItemEle) {
        var parentEleItem = regionItemEle.parent().closest('.region-item');
        if(parentEleItem.length>0) {
            var notActiveLength = parentEleItem.children('.region-children-list').children('.region-item')
                    .children('.region-title').not('.active').length;
            if(notActiveLength===0) {
                parentEleItem.children('.region-title').addClass('active');
            }
            this.addParentActive(parentEleItem);
        }
    },
    delRule:function(ruleEle) {
        var items = ruleEle.find('.selected-item');
        var that = this;
        items.each(function(){
            var regionId = $(this).data('id');
            delete that.regionRuleIds[regionId];
        });
        
        ruleEle.remove();
    },
    saveDtRules:function(name,rules) {
        if(this.updating) {
            FlashMsg.error('正在保存数据 , 请稍等...');
            return false;
        }
        
        if(!name) {
            FlashMsg.error('请输入模板名字');
            return false;
        }
        this.updating = true;
        var that = this;
        $.ajax({
            url:'/setting/delivery/create',
            data:{
                data: {
                    name:name,
                    rules:rules
                }
            },
            type:'POST',
            dataType:'json',
            success:function(r) {
                if(isResponseOk(r)) {
                    window.location.href = '/setting/delivery/view?id='+r.id;
                } else {
                    FlashMsg.error(r.msg);
                    return false;
                }
            },
            error:function() {
                
            },
            complete:function() {
                that.updating = false;
            }
        });
    }
};

$(function(){
    RegionSelect.initData();
    
    //添加新的规则地区 
    $('#btn_new_rule_region').on('click',function(e){
        e.preventDefault();
        RegionSelect.editRuleId = null;
        RegionSelect.renderNewRuleEditor();
        $('#select_area').modal('show');
    });
    
    //点击未选择地区的名称
    $('#unselected_depth0').on('click', '.region-name', function(e) {
        e.preventDefault();
        $(this).closest('.region-title').toggleClass('active');
        if( $(this).closest('.region-title').hasClass('active')) {//当前是选中的状态 所有子元素都要选中 
            $(this).closest('.region-item').find('.region-item').not('.hidden').find('.region-title').addClass('active');
            RegionSelect.addParentActive($(this).closest('.region-item'));
        } else {//当前是取消选中的状态 所有子元素都要取消选中 , 并且 父级元素也需要取消选中
            $(this).closest('.region-item').find('.region-title').removeClass('active');
            $(this).parents('.region-item').children('.region-title').removeClass('active');
        }
    });
    
    //点击加号
    $('#unselected_depth0,#selected_depth0').on('click', '.region-ladder', function(e) {
        if($(this).hasClass('active')) {//执行折叠
            $(this).html('+').removeClass('active').closest('.region-item')
                    .find('.region-children-list').addClass('hidden');
        } else {//执行展开
            $(this).html('-').addClass('active').closest('.region-item')
                    .find('.region-children-list').removeClass('hidden');
        }
    });
    
    //点击已经选择的关闭
    $('#selected_depth0').on('click', '.close', function(e) {
        var itemEle = $(this).closest('.region-item');
        RegionSelect.delSelectedRegion(itemEle);
    });
    
    //点击添加按钮
    $('#btn_add_region').on('click', function(e) {
        e.preventDefault();
        RegionSelect.addSelectedRegion();
    });
    
    //保存添加的区域
    $('#btn_save_selected_region').on('click', function(e) {
        e.preventDefault();
        RegionSelect.saveSelectedRegion();
        $('#select_area').modal('hide');
    });
    
    //点击规则的编辑按钮
    $('#dt_rule').on('click', '.btn-edit-region', function(e) {
        e.preventDefault();
        ruleEle = $(this).closest('.dt-rule');
        RegionSelect.editRuleId = ruleEle.data('id');
        RegionSelect.renderRuleEditor(ruleEle);
        $('#select_area').modal('show');
    }).on('click', '.btn-del-rule', function(e) {
        e.preventDefault();
        ruleEle = $(this).closest('.dt-rule');
        RegionSelect.delRule(ruleEle);
    });
    
    //点击保存按钮
    $('#btn_save_dt_rule').on('click', function(e) {
        e.preventDefault();
        var rules = {};
        RegionSelect.saveDtRules($('#template_name').val(),rules);
    });
});