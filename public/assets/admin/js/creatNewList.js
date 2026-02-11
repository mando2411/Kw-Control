// انشاء قائمة جديدة للاسماء
function creatNewList() {
  let listNameValue = document.getElementById("listName").value;
  let listTypeValue = document.getElementById("listType").value;
  let ta7reerContent = document.querySelector(".ta7reerContent");
  //  let cartona = [];
  console.log(listNameValue, listTypeValue);
  if (listNameValue == "" && listTypeValue == "") {
    alert("Not found data matched");
  } else {
    if (listTypeValue == "مضمون") {
      $(".ta7reerContent").addClass("bg-success").removeClass("bg-warning");
    } else {
      $(".ta7reerContent").addClass("bg-warning").removeClass("bg-success");
    }
    // const cartonaElement = document.getElementById("cartona");
    // cartona.push(`          <div class="ta7reerContent  d-flex justify-content-between px-5 py-2 align-items-center mb-4 rounded-3">
    //           <div>
    //             <span class="listName"> ${listNameValue}</span>
    //             <span class="bg-dark px-2 text-white rounded-1">0</span>
    //             <span class="listType">${listTypeValue} </span>
    //           </div>
    //           <div>
    //             <button
    //               data-bs-toggle="modal"
    //               data-bs-target="#ta7reerData"
    //               class="btn btn-dark"
    //             >
    //               تحرير
    //             </button>
    //           </div>
    //           </div>
    //         `);
    let row = "";
    let yourArray = [];

    // Loop through your array and construct the HTML
    for (let i = 0; i < yourArray.length; i++) {
      row += `
    <div class="ta7reerContent  d-flex justify-content-between px-5 py-2 align-items-center mb-4 rounded-3">
      <div>
        <span class="listName">${yourArray[i].listName}</span>
        <span class="bg-dark px-2 text-white rounded-1">${yourArray[i].count}</span>
        <span class="listType">${yourArray[i].listType}</span>
      </div>
      <div>
        <button
          data-bs-toggle="modal"
          data-bs-target="#ta7reerData"
          class="btn btn-dark">
          تحرير
        </button>
      </div>
    </div>
  `;
      yourArray.push(row);
    }

    console.log(yourArray);

    // Set the innerHTML of the #cartona div to the constructed HTML
    var cartonaElement = document.getElementById("cartona");
    cartonaElement.innerHTML = cartona;

    console.log(cartona);

    // $('#listNamemodal').val(listNameValue);
    // $('#listTypemodal').val(listTypeValue);
    // $('.listName').text(listNameValue);
    // $('.listType').text(listTypeValue);
    console.log(listTypeValue);
  }
}
