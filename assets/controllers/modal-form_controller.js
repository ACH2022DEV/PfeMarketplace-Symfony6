import {Controller} from '@hotwired/stimulus';
import * as Modal from 'bootstrap/js/dist/modal';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['modal', 'modalBody', 'modalFooter', 'modalTitle', 'modalTwo', 'modalBodyTwo', 'modalFooterTwo', 'modalTitleTwo'];
    static values = {
        modalTitle: String,
        formUrl: String,
        urlRedirect: String,
        nodeDestId: String,
        refreshKey: String,
        modalDestId: String,
        modalTitleTwo: String,
        formUrlTwo: String,
        urlRedirectTwo: String,
        nodeDestIdTwo: String,
        refreshKeyTwo: String,
        modalDestIdTwo: String
    }
    modal = null;
    modalTwo = null;

    /*
    var exampleModal = document.getElementById('exampleModal')
    exampleModal.addEventListener('show.bs.modal', function (event) {
      // Button that triggered the modal
      var button = event.relatedTarget
      // Extract info from data-bs-* attributes
      var recipient = button.getAttribute('data-bs-whatever')
      // If necessary, you could initiate an AJAX request here
      // and then do the updating in a callback.
      //
      // Update the modal's content.
      var modalTitle = exampleModal.querySelector('.modal-title')
      var modalBodyInput = exampleModal.querySelector('.modal-body input')

      modalTitle.textContent = 'New message to ' + recipient
      modalBodyInput.value = recipient
    })
     */


    async openModal(event) {
        //var BtnAttr = event.target.attributes;
        //  console.log(event);
        //  console.log(event.target.dataset);
        //  console.log(event.getTarget().getAttribute('data-form-url-value'));
        /* var exampleModal = document.getElementById('exampleModal')
         exampleModal.addEventListener('show.bs.modal', function (event) {
             var button = event.relatedTarget;
             console.log(button);
             this.formUrlValue =  button.getAttribute('data-form-url-value');
          });*/
        this.formUrlValue = event.currentTarget.dataset.formUrlValue;
        this.urlRedirectValue = event.currentTarget.dataset.urlValue;
        this.modalTitleValue = event.currentTarget.dataset.modalTitleValue;
        this.nodeDestIdValue = event.currentTarget.dataset.nodeDestIdValue;
        this.refreshKeyValue = event.currentTarget.dataset.refreshKeyValue;
        this.modalDestIdValue = event.currentTarget.dataset.modalDestIdValue;

        this.modalTitleTarget.innerHTML = this.modalTitleValue;
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        if(this.urlRedirectTwoValue !== ""){
            this.modalFooterTarget.hide()
        }
        this.modal.show();
        this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue);
    }
    async openModalTwo(event) {
        this.formUrlTwoValue = event.currentTarget.dataset.formUrlValue;
        this.urlRedirectTwoValue = event.currentTarget.dataset.urlValue;
        this.modalTitleTwoValue = event.currentTarget.dataset.modalTitleValue;
        this.nodeDestIdTwoValue = event.currentTarget.dataset.nodeDestIdValue;
        this.refreshKeyTwoValue = event.currentTarget.dataset.refreshKeyValue;
        this.modalDestIdTwoValue = event.currentTarget.dataset.modalDestIdValue;

        this.modalTitleTwoTarget.innerHTML = this.modalTitleTwoValue;
        this.modalBodyTwoTarget.innerHTML = 'Loading...';
        this.modalTwo = new Modal(this.modalTwoTarget);
        if(this.urlRedirectTwoValue !== ""){
            this.modalFooterTwoTarget.hide()
        }
        this.modalTwo.show();
        this.modalBodyTwoTarget.innerHTML = await $.ajax(this.formUrlTwoValue);


    }

    async submitForm() {
        event.preventDefault();
        if(this.modalTwo !== null && this.modalTwo !== undefined ){
            let $form = $(this.modalBodyTwoTarget).find('form');
            try {
                await $.ajax({
                    url: this.formUrlTwoValue,
                    method: $form.prop('method'),
                    data: $form.serialize(),
                    success: function (data) {
                        $('#wrapper #resultat').html(data);
                    }
                });
                console.log('successTwo!');

                this.modalTwo.hide();
                //$('#' + this.modalDestIdValue).show();

                this.dispatch('success', {
                    detail: {
                        url: this.urlRedirectTwoValue,
                        nodeDestId: this.nodeDestIdTwoValue,
                        refreshKey: this.refreshKeyTwoValue,
                    }
                });

            } catch (e) {
                this.modalBodyTwoTarget.innerHTML = e.responseText;

            }

        }else{
            let $form = $(this.modalBodyTarget).find('form');
            try {
                await $.ajax({
                    url: this.formUrlValue,
                    method: $form.prop('method'),
                    data: $form.serialize(),
                    success: function (data) {
                        $('#wrapper #resultat').html(data);
                    }
                });
                console.log('success!');
                  this.modal.hide();



                this.dispatch('success', {
                    detail: {
                        url: this.urlRedirectValue,
                        nodeDestId: this.nodeDestIdValue,
                        refreshKey: this.refreshKeyValue,
                    }
                });
                /*  this.dispatch('success', {
                      detail: {
                          url: this.urlRedirect
                      }
                  });*/

                /*   const event = new CustomEvent('success');
                   window.dispatchEvent(event);*/
                /*
                , {
                                detail: {
                                    url: this.urlRedirect
                                }
                            }
                 */
            } catch (e) {
                this.modalBodyTarget.innerHTML = e.responseText;
            }
        }
    }

    modalHidden() {
        if(this.modalTwo !== null && this.modalTwo !== undefined ){
            this.modalTwo.hide();
            this.modalTwo = null;
        }else{
            this.modal.hide();
        }
    }

}
