import {Controller} from '@hotwired/stimulus';
import {app} from "../bootstrap-table.min"


export default class extends Controller {
    static targets = ['myOneCustomContent'];//, 'myTwoCustomContent', 'myThreeCustomContent', 'myFourCustomContent', 'myFiveCustomContent'
    static values = {
        url: String,
        nodeDestId: String,
        refreshKey: String,
    }


    async refreshContent(event) {
        this.urlValue = event.detail.url
        this.nodeDestIdValue = event.detail.nodeDestId
        this.refreshKeyValue = event.detail.refreshKey
        //console.log(event);
        // console.log(this);
        //alert('refresh' + this.urlValue);

        switch (this.refreshKeyValue) {
            case "refresh-grid":
                $('#' + this.nodeDestIdValue).bootstrapTable('refresh');
                break;
            case "refresh-select":

                this.populateSelect(this.urlValue, this.nodeDestIdValue);
                break;
            default:
                // alert(this.urlValue + ' : '  + this.nodeDestIdValue);
                const responseOne = await fetch(this.urlValue);
                // document.getElementById( this.nodeDestIdValue).innerHTML = await responseOne.text();
                var elem = document.querySelector('[data-reload-content-target=' + this.nodeDestIdValue + ']');
                elem.innerHTML =  await responseOne.text();

            //document.getElementById(this.nodeDestIdValue).contentWindow.location.reload(true);
            /*switch (this.nodeDestIdValue) {
                case "myOneCustomContent":
                   // alert('1:' + this.nodeDestIdValue);
                    this.myOneCustomContentTarget.style.opacity = .5;
                    const responseOne = await fetch(this.urlValue);
                    this.myOneCustomContentTarget.innerHTML = await responseOne.text();
                    this.myOneCustomContentTarget.style.opacity = 1;
                    break;
                case "myTwoCustomContent":
                    this.myTwoCustomContentTarget.style.opacity = .5;
                    const responseTwo = await fetch(this.urlValue);
                    this.myTwoCustomContentTarget.innerHTML = await responseTwo.text();
                    this.myTwoCustomContentTarget.style.opacity = 1;
                    break;
                case "myThreeCustomContent":
                    this.myThreeCustomContentTarget.style.opacity = .5;
                    const responseThree = await fetch(this.urlValue);
                    this.myThreeCustomContentTarget.innerHTML = await responseThree.text();
                    this.myThreeCustomContentTarget.style.opacity = 1;
                    break;
                case "myFourCustomContent":
                    this.myFourCustomContentTarget.style.opacity = .5;
                    const responseFour = await fetch(this.urlValue);
                    this.myFourCustomContentTarget.innerHTML = await responseFour.text();
                    this.myFourCustomContentTarget.style.opacity = 1;
                    break;
                case "myFiveCustomContent":
                    this.myFiveCustomContentTarget.style.opacity = .5;
                    const responseFive = await fetch(this.urlValue);
                    this.myFiveCustomContentTarget.innerHTML = await responseFive.text();
                    this.myFiveCustomContentTarget.style.opacity = 1;
                    break;
            }*/
        }
    }

    /*  async refreshContentBootstrapTable(event) {
          this.contentTarget.style.opacity = .5;

          this.contentTarget.style.opacity = 1;
      }*/
    populateSelect( urlValue, nodeDestIdValue , targetId = null) {
        fetch(urlValue, {
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                const selectBox = document.querySelector('[id=' + nodeDestIdValue + ']');
                selectBox.innerHTML='';
                const  last = data.rows[data.rows.length - 1]
                data.rows.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.text = item.name;
                    opt.selected = last.id === item.id;
                    selectBox.appendChild(opt);
                });
            });
    }

}