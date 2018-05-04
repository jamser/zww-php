
accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
key = ''
expire = 0
g_object_name = ''
g_object_name_type = 'random_name'
now = timestamp = Date.parse(new Date()) / 1000;
pluploadPath = '/3rd/plupload-2.1.2/js/';

function send_request(type)
{
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp!=null)
    {
        serverUrl = '/apiv1/upload/get-oss-config?type='+type;
        xmlhttp.open( "GET", serverUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
};

function get_signature(type)
{
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000;
    if (expire < now + 3)
    {
        body = send_request(type);
        var obj = eval ("(" + body + ")");
        host = obj['host'];
        policyBase64 = obj['policy'];
        accessid = obj['accessid'];
        signature = obj['signature'];
        expire = parseInt(obj['expire']);
        callbackbody = obj['callback'];
        key = obj['dir'];
        return true;
    }
    return false;
};

function random_string(len) {
　　len = len || 32;
　　var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
　　var maxPos = chars.length;
　　var pwd = '';
　　for (i = 0; i < len; i++) {
    　　pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos).toLowerCase();
    }
    return suffix;
}

function calculate_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == 'random_name')
    {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(6) + suffix
    }
    return ''
}

function get_uploaded_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        tmp_name = g_object_name
        tmp_name = tmp_name.replace("${filename}", filename);
        return tmp_name
    }
    else if(g_object_name_type == 'random_name')
    {
        return g_object_name
    }
}

function set_upload_param(up, filename, type, callback)
{
    get_signature(type);
    g_object_name = key;
    if (filename != '') { suffix = get_suffix(filename)
        calculate_object_name(filename)
    }
    new_multipart_params = {
        'key' : g_object_name,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid,
        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
        'callback' : callbackbody,
        'signature': signature,
    };

    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });

    up.start();
}

var uploader = new plupload.Uploader({
    runtimes: 'html5,flash,silverlight,html4',
    browse_button: 'btn_select',
    multi_selection: true,
    container: document.getElementById('upload_files'),
    flash_swf_url: pluploadPath + 'Moxie.swf',
    silverlight_xap_url: pluploadPath + 'Moxie.xap',
    url: 'http://oss.aliyuncs.com',
    filters: {
        mime_types: [//只允许上传图片和zip文件
            {title: "图片文件", extensions: "jpg,png"},
            {title: "视频文件", extensions: "mp4"}
        ],
        max_file_size: '1024mb', //最大只能上传10mb的文件
        prevent_duplicates: true //不允许选取重复文件
    },
    init: {
        PostInit: function () {

        },
        FilesAdded: function (up, files) {
            plupload.each(files, function (file) {
                var html = template('tpl_upload_file_form', {
                    'fileId': file.id,
                    'fileName': file.name,
                    'fileSize': plupload.formatSize(file.size),
                });
                $('#upload_form_list').append(html);
            });
        },
        BeforeUpload: function (up, file) {
            var formEle = $('#' + file.id);
            var type = formEle.find('.input-type').val();
            set_upload_param(up, file.name, type);
        },
        UploadProgress: function (up, file) {
            var d = document.getElementById(file.id);
            d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            $('#' + file.id).find('.progress-bar').css('width', file.percent + '%').attr('aria-valuenow', file.percent);
        },
        FileUploaded: function (up, file, info) {
            if (info.status == 200)
            {
                var response = $.parseJSON(info.response);
                var formEle = $('#' + file.id);
                $.ajax({
                    url: '/apiv1/upload/save-file',
                    type: 'POST',
                    data: {
                        key: response.result,
                        name: formEle.find('.input-name').val(),
                        type: formEle.find('.input-type').val()
                    },
                    dataType: 'json',
                    success: function (r) {

                        if (isResponseOk(r)) {
                            formEle.find('.help-block').html('保存成功');
                        } else {
                            formEle.find('.help-block').html(r.msg);
                        }
                    },
                    error: function () {
                        formEle.find('.help-block').html('网络错误 , 保存失败');
                    }
                });
                //$('#'+file.id).find('.help-block').html('上传成功:'+info.response);//.data('url', info.response);
                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = 'upload to oss success, object name:' + get_uploaded_object_name(file.name) + ' 回调服务器返回的内容是:' + info.response;
            } else if (info.status == 203) {
                $('#' + file.id).find('.help-block').html('上传到OSS成功，但是oss访问用户设置的上传回调服务器失败 : ' + info.response);
                //$('#'+file.id).data('url', info.response);
                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '上传到OSS成功，但是oss访问用户设置的上传回调服务器失败，失败原因是:' + info.response;
            } else {
                $('#' + file.id).find('.help-block').html('上传失败:' + info.response);
                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            }
        },
        Error: function (up, err) {
            if (err.code == -600) {
                FlashMsg.error("选择的文件太大了");
            } else if (err.code == -601) {
                FlashMsg.error("不允许选择的该类型文件");
            } else if (err.code == -602) {
                FlashMsg.error("这个文件已经上传过一遍了");
            } else {
                FlashMsg.error("Error :" + err.response, 5000);
                //Error : InvalidPolicyDocument Invalid Policy: Invalid Conditions. 577792BDCDF474A7DF72EFBE nexttrip-files.oss-cn-hangzhou.aliyuncs.com
            }
        }
    }
});

uploader.init();
