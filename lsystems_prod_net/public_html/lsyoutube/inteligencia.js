function video_info() {
    const data = { urlToDownload: document.getElementById("urlToDownload").value}; 
    document.getElementById("loadingInfo").style.display = "block";
    $.post("http://desarrollo.lsyoutube-dl-ws/download/info", JSON.stringify(data), function(data, status){
        const formats = data;
        let formatOptions = "<option value=\"0\" selected>Default</option>\n";
        formatOptions = "<option value=\"999\">SOLO MP3</option>\n";
        formatOptions = "<option value=\"18\">MP4 (18)</option>\n";
        formats.forEach(element => {
            formatOptions += "<option  value=\"" + element.id + "\">" + element.format +"</option>\n";
        });
        document.getElementById("download_format").innerHTML = formatOptions;  
        document.getElementById("loadingInfo").style.display = "none";
        document.getElementById("downloadButton").disabled = false;
    });
}

function tabDownloadOne() {
    document.getElementById("downloadListContainer").style.display = "none";
    document.getElementById("downloadOneContainer").style.display = "block";
}


function tabDownloadList() {
    document.getElementById("downloadListContainer").style.display = "block";
    document.getElementById("downloadOneContainer").style.display = "none";
}

function download_one() {
    const data = { urlToDownload: document.getElementById("urlToDownload").value,
                    format: document.getElementById("download_format").value,
                };
    Swal.fire({
        title: 'Entendido!',
        text: 'Tu descarga estará lista en breve',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    });
    $.post("http://desarrollo.lsyoutube-dl-ws/download/one", JSON.stringify(data), function(data, status){
    });
}

function download_multiple() {
    const data = { folderToDownload: document.getElementById("folderListToDownload").value,
        urlToDownload: document.getElementById("urlListToDownload").value,
        format: document.getElementById("download_list_format").value,
    };
    Swal.fire({
        title: 'Entendido!',
        text: 'Tu descarga estará lista en breve',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    });
    $.post("http://desarrollo.lsyoutube-dl-ws/download/multiple", JSON.stringify(data), function(data, status){
    });
}