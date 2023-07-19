(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
  
        form.classList.add('was-validated')
      }, false)
    })
  })()

const asyncPost = (url, form_obj, callback = null) => {
    let result = {'msg': 'Default Error'}
    let xhr = new XMLHttpRequest();//xhr.setRequestHeader('X-Auth', '123');
    xhr.open('POST', url, true);
    xhr.send(form_obj);
    xhr.onload = () => {//console.log(xhr.responseText);
        try {
            if (xhr.status == 200) {
                result = JSON.parse(xhr.responseText.replace(/(\n)/g, "\\n"));
                if (callback) callback(result);
            }
        } catch (e) {
            result.msg = e;
            if (callback) callback(result);
        }
    }
    xhr.onerror = error => {
        result.msg = error;
        if (callback) callback(result);
    }
}

////////////////////////////////////////////////
//  sweetalert
////////////////////////////////////////////////
const swalOption = (icon, text) => {
  return {
      icon: icon,
      html: text,
      allowOutsideClick: false
  }
}

////////////////////////////////////////////////
//  Drag & Drop
////////////////////////////////////////////////
const dragStart = e => {
  e.dataTransfer.setData('text', e.target.dataset.id + '&' + e.target.dataset.schedule_datetime);
}
const drop = e => {
  e.preventDefault();
  e.target.classList.remove('dragover');
  let dropData = e.dataTransfer.getData('text').split('&');
  let dropId = dropData[0];
  let dropDate = dropData[1].split('T');
  let dropElm = document.querySelector('[data-id="' + dropId + '"]');
  let areaDate = e.target.dataset.td_date == undefined ? e.target.parentElement.dataset.td_date:e.target.dataset.td_date;
  e.currentTarget.appendChild(dropElm);
  if(dropDate[0] != areaDate){
    dropElm.dataset.schedule_datetime = areaDate + 'T' + dropDate[1];
    let fdo = new FormData();
    fdo.append('id', dropId);
    fdo.append('schedule_datetime', areaDate + ' ' + dropDate[1]);
    asyncPost('schedule_date_change.php', fdo, json => {
        if (json.result == 0) {
            location.reload();
        }
    });
  }
}
const dragOver = e => {
  e.preventDefault();
  e.target.classList.add('dragover');
}
const dragLeave = e => {
  e.preventDefault();
  e.target.classList.remove('dragover');
}


////////////////////////////////////////////////
//  selected year month
////////////////////////////////////////////////
const selectedYearMonth = (selected = 'get', year = null, month = null) => {
  let result = [];
  let y_options = select_y.options;
  let m_options = select_m.options;
  for (let i = 0; i < y_options.length; i++) {
      if(selected == 'set'){
          y_options[i].selected = y_options[i].value == year ? true : false;
      }else{
          if(y_options[i].selected){
              result.push(y_options[i].value);
          }
      }
  }
  for (let i = 0; i < m_options.length; i++) {
      if(selected == 'set'){
          m_options[i].selected = m_options[i].value == month ? true : false;
      }else{
          if(m_options[i].selected){
              result.push(m_options[i].value);
          }
      }
  }
  
  return result;
}

////////////////////////////////////////////////
//  schedule insert, update
////////////////////////////////////////////////
const scheduleUpdate = () => {
  let fdo = new FormData();
  fdo.append('id', input_id.value);
  fdo.append('title', input_title.value);
  fdo.append('schedule_datetime', schedule_datetime.value);
  fdo.append('content', textarea_content.value);
  asyncPost('schedule_update.php', fdo, json => {
      if (json.result > 0 && json.action == 'update') {
          let sche_data = document.querySelector('[data-id="' + json.id + '"]');
          document.getElementById('title_' + json.id).textContent = json.title;
          sche_data.dataset.schedule_datetime = json.schedule_datetime;
          document.getElementById('content_' + json.id).textContent = json.content;
          let schedule_datetime = json.schedule_datetime.split('T');
          let new_date_box = document.querySelector('[data-td_date="' + schedule_datetime[0] + '"]');
          new_date_box.appendChild(sche_data);
      } else {
          let DIV = document.createElement('div');
          DIV.setAttribute('class', 'schedule_data');
          DIV.setAttribute('data-bs-toggle', 'modal');
          DIV.setAttribute('data-bs-target', '#scheduleModal');
          DIV.setAttribute('data-id', json.id);
          DIV.setAttribute('data-schedule_datetime', json.schedule_datetime);
          DIV.setAttribute('draggable', true);

          let SPAN_TITLE = document.createElement('span');
          SPAN_TITLE.setAttribute('id', 'title_' + json.id);
          SPAN_TITLE.textContent = json.title;

          let SPAN = document.createElement('span');
          SPAN.setAttribute('class', 'd-none');
          SPAN.setAttribute('id', 'content_' + json.id);
          SPAN.textContent = json.content;

          DIV.appendChild(SPAN_TITLE);
          DIV.appendChild(SPAN);

          let schedule_datetime = json.schedule_datetime.split('T');
          let new_date_box = document.querySelector('[data-td_date="' + schedule_datetime[0] + '"]');
          new_date_box.appendChild(DIV);
      }
  });
}
