
window.onload = function (){

    var form = document.forms.addmsg;
    form.addEventListener("submit", sendForm , false);
    function sendForm(){
        var msgText = form.elements.textarea.value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        var sendString = 'msgText=' + encodeURIComponent(msgText);
        xhr.send(sendString);
        var ajaxResult = document.getElementById('ajaxResult');
        xhr.onreadystatechange = function() {
            if (xhr.readyState != 4) return;
            if (xhr.status == 200) {
            var json =  JSON.parse(xhr.responseText);
                if (json.error) {
                    ajaxResult.firstChild.nodeValue = json.error.msg;
                    ajaxResult.classList.add("error");
                    ajaxResult.classList.remove("sucsess");
                } else {
                    ajaxResult.firstChild.nodeValue = json.sucsess.msg;
                    ajaxResult.classList.add("sucsess");
                    ajaxResult.classList.remove("error");
                    form.reset();
                    var isLastPage = document.getElementById('isLastPage');
                    if(isLastPage) {
                        var messages = document.getElementById('messages');
                        var message = messages.getElementsByClassName('msg')[0].cloneNode(true);
                        var msgtitle = message.getElementsByClassName('msgtitle')[0];
                        var msgtitleSpan = msgtitle.getElementsByTagName('span')[0];
                        msgtitleSpan.innerHTML = json.addTime;
                        var msgtext = message.getElementsByClassName('msgtext')[0];
                        var msgtextSpan = msgtext.getElementsByTagName('span')[0];
                        msgtextSpan.innerHTML = msgText;
                        messages.appendChild(message);
                    }
                }
            } else {
              ajaxResult.firstChild.nodeValue = "Ошибка соединения";
        }
      }
    }
}
