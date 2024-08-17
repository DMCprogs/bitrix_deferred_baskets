let open_modal = document.querySelectorAll('.open_modal');
let body = document.getElementsByTagName('body')[0];
open_modal.forEach(element => {
    element.onclick = function () {
        let idBasket = element.attributes.data_id.value;
        let userOpt = element.attributes.data_user.value;
        // Создаем элемент модального окна
        const modal = document.createElement('div');
        modal.id = 'modal_deffered';
        modal.className = 'modal_deffered bounceIn modal_vis';
        body.classList.add('body_block');
        // HTML-контент модалки
        modal.innerHTML = `
     
     <div class="modal_txt">
     <div id="close_modal" onclick="closeModal()">+</div>
     <div class="content-modal"> <div class="text-basket">Востановление корзины очистит вашу текущую корзину вы уверены?</div>
     <div class="button-modal"> <button onclick="closeModal()"  class="restore-order not-modal">
         Нет
       </button>
       <button  class="restore-order deffered-cart" onclick="ajaxBasketRestore(${idBasket},'${userOpt}')">
         Да
       </button></div>
     </div>
    
     </div>
   `;




        document.body.insertAdjacentElement('afterbegin', modal);// клик на открытие
        //    modal_created.classList.add('modal_vis'); // добавляем видимость окна
        //    modal_created.classList.remove('bounceOutDown'); // удаляем эффект закрытия
        //         body.classList.add('body_block'); // убираем прокрутку
    };
});

function closeModal() {
    let modal_created = document.getElementById('modal_deffered');
    modal_created.classList.add('bounceOutDown'); // добавляем эффект закрытия
    window.setTimeout(function () { // удаляем окно через полсекунды (чтобы увидеть эффект закрытия).
        modal_created.classList.remove('modal_vis');
        body.classList.remove('body_block'); // возвращаем прокрутку
    }, 500);
}

function ajaxBasketRestore(dataId, userOpt) {

    let deffered_items =document.querySelectorAll("#item_restore");
    let id_items=[];
    let allChecked = true;
    deffered_items.forEach(element => {
        if (!element.checked) {
            allChecked = false;
            
        }
    });
    
    // Если все элементы checked, очищаем массив id_items
    if (allChecked!=false) {
        id_items = [];
        console.log("все товары из корзины выбранны");
    } else {
        console.log("не все товары из корзины выбранны");
        // Иначе, заполняем массив id_items
        deffered_items.forEach(element => {
            if (element.checked) {
                id_items.push(element.attributes.data_id.value);
            }
        });
    }
    
    var param = 'elementId=' + dataId + "&user=" + userOpt+"&item_id="+JSON.stringify(id_items);
    
        $.ajax({

            url: '/ajax/add_1c_price.php', // URL отправки запроса
            type: "GET",
            dataType: "html",
            data: param,

            success: function (response) {

                try {
                    $.ajax({

                        url: '/ajax/restore-basket.php', // URL отправки запроса
                        type: "GET",
                        dataType: "html",
                        data: param,

                        success: function (response) {

                            try {
                                location.reload();
                            } catch (error) {
                                console.error("Error parsing JSON:", error);
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) { // Если ошибка, то выкладываем печаль в консоль
                            console.log('Error: ' + errorThrown);
                        }
                    });

                } catch (error) {
                    console.error("Error parsing JSON:", error);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) { // Если ошибка, то выкладываем печаль в консоль
                console.log('Error: ' + errorThrown);
            }
        });
    
    

}



addEventListener("DOMContentLoaded", (event) => {

    let openOrder = document.querySelectorAll(".open-order");
    let restoreOrder = document.querySelectorAll(".restore-order");

    openOrder.forEach(element => {
        element.addEventListener('click', function () {
            console.log(element);
            let containerOrder = element.nextElementSibling;
            if (containerOrder.style.display === "none") {

                containerOrder.style.display = "block";
            }
            else {
                containerOrder.style.display = "none";
            }

        });
    });



});





