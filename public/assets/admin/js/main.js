let newDate = new Date();
$(".todayDate").text(newDate.getDate());

(function () {
  $(".moreSearch div.btn").on("click", function () {
    $(this).siblings(".moreSearch .description").toggleClass("d-none");
  });
})();

// elmadameen

$("#filterPerTrust").on("change", function () {
  console.log();
  $(".percentage").text($(this).val());
});

$("#trustingRate").on("change", function () {
  console.log();
  $("#trustingRate + span span").text($(this).val());
});

(function () {
  function copyToClipboard(text) {
    navigator.clipboard
      .writeText(text)
      .then(() => {
        console.log("Text copied to clipboard:", text);
      })
      .catch((error) => {
        console.error("Failed to copy text:", error);
      });
  }

  $(".copyLink").on("click", function () {
    copyToClipboard(this.value);
  });
})();

// elmota3ahdeen
// choise radio input background
(function () {
    $(".checkMota3ahed label input").on("change", function(){
      if ($(this).is(":checked")){
        console.log($(this));
        if (
          $(this)
          .parent()
          .hasClass('btn-outline-success')
        ) {
          console.log('success');
          $(this)
            .parent()
            .removeClass("btn-outline-success")
            .addClass("btn-success");

                  $(this)
                    .parent()
                    .siblings()
                    .addClass("btn-outline-warning")
                    .removeClass("btn-warning");
        }
       else if (
          $(this)
          .parent()
          .hasClass('btn-outline-warning')
        ) {
          console.log('warning');
          $(this)
            .parent()
            .removeClass("btn-outline-warning")
            .addClass("btn-warning");

                  $(this)
                    .parent()
                    .siblings()
                    .addClass("btn-outline-success")
                    .removeClass("btn-success");

        }
       else if (
          $(this)
          .parent()
          .hasClass('btn-outline-secondary')
        ) {
          console.log("secondary");
          $(this)
            .parent()
            .removeClass("btn-outline-secondary")
            .addClass("btn-secondary");

                  $(this)
                    .parent()
                    .siblings()
                    .addClass("btn-outline-danger")
                    .removeClass("btn-danger");

        }
       else if (
          $(this)
          .parent()
          .hasClass('btn-outline-danger')
        ) {
          console.log("danger");
          $(this)
            .parent()
            .removeClass("btn-outline-danger")
            .addClass("btn-danger");

                  $(this)
                    .parent()
                    .siblings()
                    .addClass("btn-outline-secondary")
                    .removeClass("btn-secondary");

        }
      }



    });
  })();

(function () {
  $("th button.all").on("click", getAllInputChecked);
})();
var checkedValues = [];

function getAllInputChecked() {
  // console.log(
  //   $(this).parent().parent().parent().siblings().find("input.check")
  // );
  if (
    $(this)
      .parent()
      .parent()
      .parent()
      .siblings()
      .find("input.check")
      .prop("checked") == true
  ) {
    $(this)
      .parent()
      .parent()
      .parent()
      .siblings()
      .find("input.check")
      .prop("checked", false);
    // console.log("aaa");
    $(".listNumber").text(0);
    checkedValues=[];
  } else {
    $(this)
      .parent()
      .parent()
      .parent()
      .siblings()
      .find("input.check")
      .prop("checked", true);
      $(this)
          .parent()
          .parent()
          .parent()
          .siblings()
          .find("input.check:checked")
          .each(function() {
              checkedValues.push($(this).val());
          });
          console.log(checkedValues);


    $(".listNumber").text(
      $(this).parent().parent().parent().siblings().find("input.check").length
    );
}
}

$("#all_voters").on('click',function(){

    if(!checkedValues){
        var checkedElements = $('.check:checked');

    var checkedValues = $.map(checkedElements, function(element) {
    return $(element).val();
    });

    }
    checkedValues.forEach(function(value) {
        $('<input>').attr({
            type: 'hidden',
            name: 'voter[]',
            value: value
        }).appendTo('#form-attach');
    });
        if(confirm("تاكيد العمليه")){
        $('#form-attach').submit();

    }

});


// sorting
(function () {
  $(".plusBtn").on("click", function () {
    let soundCountNumber = +$(this).parent().siblings(".soundCount").text();
    let totalSortingSound = +$(".totalSortingSound").text();
    totalSortingSound++;
    soundCountNumber++;
    $(this).parent().siblings(".soundCount").text(soundCountNumber);
    $(".totalSortingSound").text(totalSortingSound);
    // console.log(soundCountNumber);
  });

  $(".minusBtn").on("click", function () {
    let soundCountNumber = +$(this).parent().siblings(".soundCount").text();
    let totalSortingSound = +$(".totalSortingSound").text();
    soundCountNumber--;
    if (soundCountNumber <= 0) {
      soundCountNumber = 0;
      $(this).parent().siblings(".soundCount").text(soundCountNumber);
    } else {
      $(this).parent().siblings(".soundCount").text(soundCountNumber);

      totalSortingSound--;
      $(".totalSortingSound").text(totalSortingSound);
    }
  });


  $('td[data-bs-target="#nameChechedDetails"]').on("click", function () {
    let id=$(this).siblings("#voter_td").children("#voter_id").val();
    console.log(id);
    let con_id =$("#con_id").val()
    var url = '/voter/' + id + '/' + con_id;
    axios.get(url)
            .then(function (response) {
                console.log('Success:', response);

                $("#mota3ahedDetailsVoterId").val(response.data.voter.id)
                $("#mota3ahedDetailsName").val(response.data.voter.name)
                $("#mota3ahedDetailsPhone").val(response.data.voter.phone1)
                $("#mota3ahedDetailsCommitte").val(response.data.committee_name)
                $("#mota3ahedDetailsRegiterNumber").val(response.data.voter.alsndok)
                $("#mota3ahedDetailsSchool").val(response.data.school)
                $("#mota3ahedDetailsTrustingRate").val(response.data.percent)
                $("#percent").text(response.data.percent)
                $("#father").val(response.data.voter.father)

                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert('Failed to set votes.');
            });

            $("#mota3ahedDetailsTrustingRate").on("change",function(){
                let val = $(this).val()
                $("#percent").text(val)
                var url = '/percent/' + id + '/' + con_id + '/' + val;
                axios.get(url)
            .then(function (response) {
                console.log('Success:', response);
                if(confirm('تأكيد التعديل ')){
                    alert('تم التعديل بنجاح')
                    window.location.reload();
                }
                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert('Failed to set votes.');
            });
            })

})


  $('td[data-bs-target="#candidateSounds"]').on("click", function () {
    $(".modalCondidateName").text($(this).siblings(".fullName").text());
    $("#totalSound").val($(this).siblings(".soundCount").text());
    $("#Model_committee").val($(this).siblings("#committee").val())
    $("#Model_id").val($(this).siblings("#candi").val())
    let currentSoundCount = $(this).siblings(".soundCount");
    let theCurrentSoundBefourAdd = currentSoundCount.text();
    $(".editTotalSoundBtn").on("click", function () {
        var modelId = $("#Model_id").val();
        var votes = $("#totalSound").val();
        var committee = $("#Model_committee").val();
        var url = 'candidates/' + modelId + '/votes/set';
        var data = {
            votes: votes,
            committee: committee
        };
        axios.post(url, data)
            .then(function (response) {
                console.log('Success:', response);
                if (confirm('Votes set successfully! Click OK to refresh the page.')) {
                    window.location.reload();
                }
                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert('Failed to set votes.');
            });
    });
  });
  $('td[data-bs-target="#settingSearchName"]').on("click", function () {
    // Retrieve the sibling elements' values and set them in the form inputs
    $("#nameMandoob").val($(this).siblings("#user_name").text());
    $("#phoneMandoob").val($(this).siblings("#user_phone").text());
    $("#id").val($(this).siblings("#user_id").val());

});
$('td[data-bs-target="#newMemberModal"]').on("click", function () {
    var id=$(this).siblings("#user_id").text();
    var url = '/user/' + id ;
    axios.get(url)
    .then(function (response) {
        console.log('Success:', response.data.user);
    $("#newMemberModalName").val(response.data.user.name)
    $("#newMemberModalPhone").val(response.data.user.phone)
    $("#u_id").val(id);
    console.log(response.data.user.id);

                })
    .catch(function (error) {
        console.error('Error:', error);
        alert(error.response.data.message);
    });
    $('#edit').on('click', function(){
      $('edit-user').submit();
    })


});


$('td[data-bs-target="#mota3ahdeenData"]').on("click", function () {
    // var id=$("#user_id").val();
    var id=$(this).siblings("#user_id").text();
    var conUrl=$(this).siblings("#conUrl").data('url');
    var url = '/con/' + id ;
    let cartona = "";
    let deletes = "";
    let logs_ar = "";
    let resultSearchDate = document.getElementById("voters_con");
    let deletes_data = document.getElementById("deletes_data");
    let log_data = document.getElementById("log_data");



    axios.get(url)
            .then(function (response) {
                console.log('Success:', response.data.user.logs);
                $("#last_id").val(response.data.user.id)
            $("#nameMota3ahed").val(response.data.user.name)
            $("#phoneMota3ahed").val(response.data.user.phone)
            $("#trustingRate").val(response.data.user.trust)
            $("#trustText").text(response.data.user.trust)
            $("#noteMota3ahed").val(response.data.user.note)
            $("#changparent_id").val(response.data.user.parent)
            $('#delete-con').val(response.data.user.id)
            $('#voters_numberss').text(" "+response.data.user.voters.length + " ")
            $('#deletes_count').text(" "+response.data.user.softDelete.length + " ")
            $('#log_count').text(" "+response.data.user.logs.length + " ")


            if(response.data.user.softDelete.length > 0){
                $(".Delete_names ").removeClass("d-none")
            }
            if(response.data.user.logs.length > 0){
                $(".Search_logs ").removeClass("d-none")
            }

            let phoneNumber = Number(response.data.user.phone.replace(/\s+/g, ''));
            $('#phone_wa').val(phoneNumber)
            if(response.data.user.status){
                if(response.data.user.status == 1){
                    $('#moltazem').attr('checked',true)
                    $('#moltazem-l').removeClass('btn-outline-success')
                    $('#moltazem-l').addClass('btn-success')

                }else{
                    $('#follow').attr('checked',true)
                    $('#follow-l').removeClass('btn-outline-warning')
                    $('#follow-l').addClass('btn-warning')

                }
            }
            let voters= response.data.user.voters
            let softDelete= response.data.user.softDelete
            let logs= response.data.user.logs
            $("#con-url").text(conUrl)
            $("#RedirectLink").attr('href', conUrl);
            let candi=response.data.user.creator+": اخوكم المرشح"
            let message= $('#message').val()+ candi

            document.getElementById('whatsapp-link').href = `https://wa.me/965${phoneNumber}?text=${encodeURIComponent(message)}%0A%0A${encodeURIComponent(conUrl)}`;

            $("#phone_wa").on("change", function() {
                let phone = $(this).val();
                document.getElementById('whatsapp-link').href = `https://wa.me/965${phone}?text=${encodeURIComponent(message)}%0A%0A${encodeURIComponent(conUrl)}`;
            });
              $('#delete-con').on('click',function(){
                if(confirm('تأكيد حذف المتعهد ')){
                    var contractorId =$(this).val();
                    var delete_url = `/dashboard/contractors/${contractorId}`;
                    axios.delete(delete_url)
            .then(response => {
                console.log(response.data);

                if (response.status === 200) {
                    console.log('Deleted successfully');

                    alert('تم حذف المتعهد بنجاح');
                    window.location.reload();

                }
            })
            .catch(error => {
                console.error('Error deleting:', error);
                alert('Error deleting contractor');
            });

                    }

            })
            for (let i = 0; i < voters.length; i++) {
                var id = voters[i].id;
                console.log(voters[i].status);
                let status="";
                if(voters[i].status == true){
                  let updatedAt = new Date(voters[i].updated_at);

                  let formattedDate = updatedAt.toLocaleDateString("en-GB") + ' ' + updatedAt.toLocaleTimeString("en-GB", { hour: '2-digit', minute: '2-digit' });

                  status=`
                               <span><i class="fa fa-check-square text-success ms-1"></i>تم التصويت </span>
                                <span>${ formattedDate }</span> `
                }
                cartona += `  <tr>
                            <td>
                              <input
                                type="checkbox"
                                class="check"
                                name="voter[]"
                                value="${id}"
                              />
                            </td>
                            <td>${i+1}</td>
                            <td>
                                ${voters[i].name}
                            </td>
                            <td>${voters[i].pivot.percentage}%</td>
                            <td>${voters[i].phone1}</td>
                            <td>
                                ${status}
                            </td>
                          </tr>
                        `;

            }
            for (let i = 0; i < softDelete.length; i++) {
                var id = softDelete[i].id;
                deletes += `  <tr>
                            <td>${i+1}</td>
                            <td>
                                ${softDelete[i].name}
                            </td>
                            <td>${softDelete[i].phone1}</td>
                            <td></td>
                          </tr>
                        `;

            }


            for (let i = 0; i < logs.length; i++) {
                var id = logs[i].id;
                logs_ar += `  <tr>
                            <td>
                                ${logs[i].description}
                            </td>
                            <td>${logs[i].created_at}</td>
                          </tr>
                        `;

            }


            resultSearchDate.innerHTML = cartona;
            log_data.innerHTML = logs_ar;
            deletes_data.innerHTML = deletes;


                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert(error.response.data.message);
            });
            $('[data-bs-target="edit-mot"]').on('change', function() {
                var elementName = $(this).attr('name') || $(this).prop('tagName').toLowerCase();
                var elementValue = $(this).val();
                var api = 'mot-up/' + id ;
                if(confirm('تأكيد تعديل المتعهد ')){


        var data = {
            name: elementName,
            value: elementValue
        };
        axios.post(api, data)
            .then(function (response) {
                console.log('Success:', response);
                alert(response.data.message)
                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert(error.response.data.message);
            });

                }


            });


    $(".modalCondidateName").text($(this).siblings(".fullName").text());
    $("#totalSound").val($(this).siblings(".soundCount").text());
    $("#Model_committee").val($(this).siblings("#committee").val())
    $("#Model_id").val($(this).siblings("#candi").val())
    let currentSoundCount = $(this).siblings(".soundCount");
    let theCurrentSoundBefourAdd = currentSoundCount.text();
    $(".editTotalSoundBtn").on("click", function () {
        var modelId = $("#Model_id").val();
        var votes = $("#totalSound").val();
        var committee = $("#Model_committee").val();
        var url = 'candidates/' + modelId + '/votes/set';
        var data = {
            votes: votes,
            committee: committee
        };
        if (confirm('تاكيد تعديل الاصوات')) {
        axios.post(url, data)
            .then(function (response) {
                console.log('Success:', response);
                    window.location.reload();
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    alert('Failed to set votes.');
                });
            }
    });

    $(".Delete_names h5").on("click", function(){
        $(this).siblings().toggleClass("d-none")

    })
    $(".Search_logs h5").on("click",function(){
        $(this).siblings().toggleClass("d-none")
    })

  });

// Attach a single event listener to the submit button
$("#submit-form").on("click", function (e) {
    // Prevent default form submission
    e.preventDefault();

    if (confirm("تأكيد العمليه")) {

    // Retrieve the values from the input fields
    var modelId = $("#id").val();
    console.log(modelId);
    var name = $("#nameMandoob").val(); // Corrected input field
    var phone = $("#phoneMandoob").val(); // Corrected input field
    var committee_id = $("#committeMandoob").val();

    // Construct the URL for the POST request
    var url = 'rep/' + modelId + '/update'; // Ensure the URL starts with `/`

    // Create the data object to be sent in the request
    var data = {
        name: name,
        phone: phone,
        committee_id: committee_id
    };

    // Make the Axios POST request with CSRF token
    axios.post(url, data, {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Including CSRF token
        }
    })
    .then(function (response) {
        console.log('Success:', response);
        alert(response.data.message);
        window.location.reload();

        // Show confirmation and reload page if confirmed

    })
    .catch(function (error) {
        console.error('Error:', error);
        alert(error.response.data.message);
    });
}
});

})();


// el committes
(function () {
  $(".controls button").on("click", function () {
    console.log();
    let filiterName = $(this).attr("data-filiter");
    switch (filiterName) {
      case "man":
        // console.log('man');
        $("tbody").addClass("d-none");
        $("tbody.man").removeClass("d-none");

        break;
      case "weman":
        // console.log('woman');
        $("tbody").addClass("d-none");
        $("tbody.weman").removeClass("d-none");
        break;
      case "all":
        // console.log('all');
        $("tbody.man").removeClass("d-none");
        $("tbody.weman").removeClass("d-none");
        break;

      default:
        break;
    }
    // if (filiterName == 'man') {

    //   }else{
    //   console.log('weman');
    //   $('tbody.man').addClass('d-none');

    // }
  });
})();



// قائمة اسماء البحث بالكشوف

  // انشاء قائمة جديدة للاسماء
  // function creatNewList() {
  //   let listNameValue = document.getElementById("listName").value;
  //   let listTypeValue = document.getElementById("listType").value;
  //   let ta7reerContent = document.querySelector(".ta7reerContent");
  //   let cartona = "";
  //   console.log(listNameValue, listTypeValue);
  //   if (listNameValue == "" && listTypeValue == "") {
  //     alert("Not found data matched");
  //   } else {
  //     if (listTypeValue == "مضمون") {
  //       $(ta7reerContent).addClass('bgMadmoon');


  //       $('.ta7reerContent.bgMadmoon')
  //       .addClass("bg-success")
  //       .removeClass("bg-warning")
  //       $(".ta7reerContent").removeClass("bgMadmoon");
  //     } else  {
  //             $(ta7reerContent).addClass('bgRevesion');

  //       $(".ta7reerContent.bgRevesion")
  //         .addClass("bg-warning")
  //         .removeClass("bg-success");

  //         $(".ta7reerContent").removeClass("bgRevesion");
  //     }


  //     cartona += `
  //               <div class="ta7reerContent d-flex py-2 rounded-3 flex-column">
  //           <div class="d-flex justify-content-between align-items-center mb-2" >
  //             <div>
  //               <span class="listName"> ${listNameValue}</span>
  //               <span class="bg-dark px-2 text-white rounded-1">0</span>
  //               <span class="listType">${listTypeValue} </span>
  //             </div>
  //             <div>
  //               <button
  //                 data-bs-toggle="modal"
  //                 data-bs-target="#ta7reerData"
  //                 class="btn btn-dark"
  //               >
  //                 تحرير
  //               </button>
  //             </div>
  //           </div>
  //         </div>
  //           `;

  //     ta7reerContent.innerHTML += cartona;
  //   }
  // }

  $(function () {
    $(".ta7reerContent .ta7reerList").on('click' , function(){
      $(this).siblings().toggleClass('d-none')
    });
  //  console.log();
   });

  // home countDown

// Deadline date

// Get Start/End Date and Time if elements exist on the page
const startDateEl = document.getElementById('startDate');
const startTimeEl = document.getElementById('startTime');
const endDateEl = document.getElementById('endDate');
const endTimeEl = document.getElementById('endTime');

if (startDateEl && startTimeEl && endDateEl && endTimeEl) {
  // Get Start Date and Time
  let startDate = startDateEl.value; // Example: "2025-06-30"
  let startTime = startTimeEl.value; // Example: "15:28:00"

  // Get End Date and Time
  let endDate = endDateEl.value; // Example: "2024-06-30"
  let endTime = endTimeEl.value; // Example: "15:28:10"

  // Create valid Date objects
  let targetDate = new Date(`${startDate}T${startTime}`);
  if (isNaN(targetDate.getTime())) {
    console.error("Invalid Start Date or Time");
  }
  targetDate = targetDate.getTime();

  let endTargetDate = new Date(`${endDate}T${endTime}`);
  if (isNaN(endTargetDate.getTime())) {
    console.error("Invalid End Date or Time");
  }
  endTargetDate = endTargetDate.getTime();

  // Countdown Timer
  (function updateTime() {
    const currentDate = new Date().getTime();
    const timeDifference = targetDate - currentDate;

    const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
      (timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

    $("#days").text(formatTime(days));
    $("#hours").text(formatTime(hours));
    $("#minutes").text(formatTime(minutes));
    $("#seconds").text(formatTime(seconds));

    if (targetDate <= currentDate) {
      $("#election_start").addClass('d-none')
        $("#election_end").removeClass('d-none')
        $(".time-election").text("باقى على انتهاء الانتخابات");
        targetDate = endTargetDate; // Switch to end date
        if (targetDate <= currentDate) {
            $(".time-election").text("تم انتهاء الانتخابات");

            $("#days").text("٠٠");
            $("#hours").text("٠٠");
            $("#minutes").text("٠٠");
            $("#seconds").text("٠٠");
            return; // Exit the function
        }
    }
    setTimeout(updateTime, 1000);
})();

}

// Convert numbers to Arabic numerals
function toArabicNumerals(number) {
  const arabicNumbers = {
    0: '٠',
    1: '١',
    2: '٢',
    3: '٣',
    4: '٤',
    5: '٥',
    6: '٦',
    7: '٧',
    8: '٨',
    9: '٩'
  };

  return number.toString().split('').map(digit => arabicNumbers[digit]).join('');
}

// Format time and convert to Arabic numerals
function formatTime(time) {
  const formattedTime = time < 10 ? `0${time}` : time;
  return toArabicNumerals(formattedTime);
}


  // البحث بجدول المتعهدين الفرعيين
  let subMota3ahdeenName = [
    { name: "انفال فيصل حسين على جديع اباالصافى"},
    { name: "عزة حسام حسين على جديع اباالخيل"},
    { name: "احمد على محمد مسعد هانى ابا صفا" },
    { name: "خخخخخخ على محمد مسعد هانى ابا صفا"},
    { name: " خالد السيد على محمد بديع الابراهيمى"},
  ];

  // mota3ahdeeen
  function getMota3ahdeenName() {
    let searchSubMota3ahdeenNameInput = document.getElementById(
      "mota3ahdeenTableSearch"
    );
    let searchSubMota3ahdeenNameValue = searchSubMota3ahdeenNameInput.value;
    let cartona = "";

    if (searchSubMota3ahdeenNameValue == "") {
      alert("Not found data matched");
    } else {

      for (let i = 0; i < subMota3ahdeenName.length; i++) {

        if (subMota3ahdeenName[i].name.includes(searchSubMota3ahdeenNameValue)) {

          cartona += `
          <tr>
                  <td>
                    <input
                      type="checkbox"
                      class="check"
                      name="subMota3ahdeenNameChecked"
                    />
                  </td>
                  <td>1</td>
                  <td data-bs-toggle="modal" data-bs-target="#mota3ahdeenData">
                      ${subMota3ahdeenName[i].name}
                  </td>
                  <td>7721654</td>
                  <td>0</td>
                  <td>2</td>
                  <td>0%</td>
                  <td>2</td>
                </tr>`;
        }
      }
      $('#searchSubMota3ahdeenName').html(cartona)
    }
  }

  // abdallah
  (function () {
    $(".allBtn").on("click", getInputChecked);
  })();

  function getInputChecked() {
    console.log(
      $(this).parent().siblings().children().find("input.check")
    );
    if (
      $(this)
        .parent()
        .siblings()
        .children()
        .find("input.check")
        .prop("checked") == true
    ) {
      $(this)
        .parent()
        .siblings()
        .children()
        .find("input.check")
        .prop("checked", false);
      // console.log("aaa");
      $(".namesListCounter").text(0);
    } else {
      $(this)
        .parent()
        .siblings()
        .children()
        .find("input.check")
        .prop("checked", true);

      $(".namesListCounter").text(
        $(this).parent().siblings().children().find("input.check").length
      );
    }
  }

  // abdallah , el madameen
  (function () {
    $(" .madameenTable input ").on("click",getCheckedCounter);

    function getCheckedCounter() {
      // console.log("checked");
      if ($(this).prop("checked") == true) {
        let lastListNumber = +$(".listNumber").text() + 1;
        $(".listNumber").text(lastListNumber);
      } else {
        let lastListNumber = +$(".listNumber").text() - 1;
        $(".listNumber").text(lastListNumber);
      }
    }
  })();

  // elkshoof
  // البحث بجدول العائلات
  let familysName = [
    { name: " الرشيدى"},
    { name: " اباالصافى"},
    { name: " اباالخيل"},
    { name: " ابا صفا" },
    { name: " المطيرى"},
    { name: " الابراهيمى"},
  ];

  // elkshoof
  function searchByFamily() {
    let searchByFamily = document.getElementById(
      "searchByFamily"
    );
    let searchByFamilyValue = searchByFamily.value;
    // console.log(searchByFamilyValue);
    let cartona = "";

    if (searchByFamilyValue == "") {
      alert("Not found data matched");
    } else {
      for (let i = 0; i < familysName.length; i++) {
        if (familysName[i].name.includes(searchByFamilyValue)) {
          cartona += `
          <tr>
                      <td >
                          <a href="searchInLists.html">
                            <button  class="btn btn-outline-dark"><i class="fa fa-magnifying-glass"></i></button>
                          </a>
                      </td>
                      <td>${familysName[i].name}</td>
                      <td class="table-primary">24231</td>
                      <td class="table-danger">564132</td>
                      <td>23484866</td>
                      <td >
                          <button data-bs-toggle="modal" data-bs-target="#elkshoofDetails" class="btn btn-outline-dark">كشوف</button>
                      </td>
                  </tr>
          `;

        }
      }
      $("#searchFamilyData").html(cartona);
    }
  }

  // sorting
  // بحث باسم المرشح
  let candidatesName = [
    { name: " احمد رشيد حامد مرزوق مزيد بن صبح الرشيدى" },
    { name: " أميره نواف عبد الله المطيري" },
    { name: " العنود سلطان نايف المطيرى" },
    { name: " امانى نواف سالم الشمرى" },
    { name: " خخخخ" },

  ];

  // sorting
  function searchByCandidateName() {
    let candidateNameInput = document.getElementById("candidateName");
    let candidateNameValue = candidateNameInput.value;
    // console.log(candidateNameValue);
    let cartona = "";

    if (candidateNameValue == "") {
      alert("Not found data matched");
    } else {
      for (let i = 0; i < candidatesName.length; i++) {
        if (candidatesName[i].name.includes(candidateNameValue)) {
          cartona += `
          <tr>
                  <td>1</td>
                  <td>
                    <button class="btn btn-success w-100 plusBtn">+</button>
                  </td>
                  <td class="fullName">
                  ${candidatesName[i].name}
                  </td>
                  <td>
                    <button class="btn btn-danger w-100 minusBtn">-</button>
                  </td>
                  <td class="soundCount">2</td>
                  <td data-bs-toggle="modal" data-bs-target="#candidateSounds">
                    <i
                      class="fa fa-user-pen bg-secondary text-white p-2 rounded-2"
                    ></i>
                  </td>
                </tr>
          `;
        }
      }
      $(".resultcandidateName").html(cartona);
    }
  }

  // elma7zofeen
  // البحث بجدول المتعهدين الفرعيين
  let searchSubMota3ahdeenName = [
    { name: "انفال فيصل حسين على جديع اباالصافى"},
    { name: "عزة حسام حسين على جديع اباالخيل"},
    { name: "احمد على محمد مسعد هانى ابا صفا" },
    { name: "خخخخخخ على محمد مسعد هانى ابا صفا"},
    { name: " خالد السيد على محمد بديع الابراهيمى"},
  ];

  // mota3ahdeeen
  function getSubMota3ahdeenName() {
    let searchSubMota3ahdeenNameInput = document.getElementById(
      "searchInMota3ahdeenTable"
    );
    let searchSubMota3ahdeenNameValue = searchSubMota3ahdeenNameInput.value;
    let cartona = "";

    if (searchSubMota3ahdeenNameValue == "") {
      alert("Not found data matched");
    } else {

      for (let i = 0; i < searchSubMota3ahdeenName.length; i++) {

        if (searchSubMota3ahdeenName[i].name.includes(searchSubMota3ahdeenNameValue)) {

          cartona += `
          <tr>
                    <td>150</td>
                    <td
                      data-bs-toggle="modal"
                      data-bs-target="#ma7azofeenMota3ahdeenData"
                    >
                    ${searchSubMota3ahdeenName[i].name}
                    </td>
                    <td>7721654</td>
                    <td>0</td>
                    <td>
                      <span>12-05-2024</span>
                      <span>03:05:16</span>
                    </td>
                  </tr>
          `;


        }
      }
      $('.searchSubMota3ahdeenName').html(cartona)
    }
  }

