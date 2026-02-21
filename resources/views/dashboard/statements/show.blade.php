    @extends('layouts.dashboard.app')

    @section('content')
    <style>


    .table>:not(caption)>*>*{
        /* padding: 10px; */
        font-size: 14px;
    }
    @media screen and (max-width: 767px){
    .projectContainer{width: auto;}
    .table>:not(caption)>*>*{
        padding: 0px;
        font-size: 10px;
    }

}

#copyConUrlBtn.is-copying {
    opacity: .85;
    transform: translateY(-1px);
}

#copyConUrlBtn.is-copied {
    animation: copyPulse .45s ease;
}

@keyframes copyPulse {
    0% { transform: scale(1); }
    45% { transform: scale(1.06); }
    100% { transform: scale(1); }
}

#load-more {
    background-color: #007bff;  /* Primary blue color */
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 50px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

#load-more:hover {
    background-color: #0056b3;  /* Darker blue on hover */
    transform: scale(1.05);  /* Slightly enlarge on hover */
}

#load-more:active {
    transform: scale(0.95);  /* Slightly shrink on click */
}

/* Loading spinner styles */
#load-more.loading::after {
    content: '';
    width: 20px;
    height: 20px;
    border: 3px solid white;
    border-top: 3px solid transparent;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    animation: spinner 0.8s linear infinite;
}

/* Spinner animation */
@keyframes spinner {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}


    </style>


    <div class="projectContainer mx-auto">
        <section class=" rtl">
            <div class="container ">
            <form class="memberForm d-flex">
                    <input type="search" class="form-control py-1  mb-1 border border-dark border-2" placeholder="بحث بين المضامين" name="madameeen" id="madameeen">
                    <button type="submit" class="btn btn-dark mx-2 mb-1">بحث</button>
                    <input type="search" class="form-control  mb-1 border border-dark border-2" placeholder="بحث بين المتعهدين" name="mota3hdeeen" id="mota3hdeeen">

            </form>
            </div>
        </section>

        <section class="">
            <div class="table-responsive mainTable p-2">
                <table class="tab-war table table-striped-columns rtl overflow-scroll text-center">
                    <thead class="table-dark border-0 border-dark border-bottom border-2" >
                        <tr>
                            <th>#</th>
                            <th class="">أسماء المضامين</th>
                            <th class="w30">هاتف المضمون</th>
                            <th>الالتزام</th>
                            <th class="">اسم المتعهد</th>
                            <th class="w30">مضامين</th>
                            <th>الصدق</th>
                            <th>هاتف المتعهد</th>
                            <th>لجنة</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-dark fw-bold" id="userData">


                </tbody>
            </table>
        </div>
    </section>

        <!-- Modal madameenName-->
                <div
                class="modal modal-md rtl"
                id="madameen"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        بيانات الناخب
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                    </div>
                    <div class="modal-body">
                    <div class="d-flex align-items-center mb-1 mb-3">
                        <label class="labelStyle" class="" for="id">الرقم المدنى</label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1"
                        name="id"
                        id="id"
                        />
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3">
                        <label class="labelStyle" class="" for="name">الاسم الكامل </label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1"
                        name="name"
                        id="name"
                        value=""
                                        />
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3">

                        <div class="d-flex align-items-center mb-1 w-50 me-2">
                        <label class="labelStyle" class="w-100" for="family">العائلة </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1 w-50"
                            name="family"
                            id="family"

                            value=""
                                            />
                        </div>
                    </div>
                    <hr />
                    <div class="d-flex align-items-center mb-1 mb-3">
                        <label class="labelStyle" class="" for="phone1"> الهاتف </label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1"
                        name="phone1"
                        id="phone1"
                        value="144254435"
                        min="0"
                        />
                        <a href="" class="d-inline-block px-4 text-white bg-primary"
                        ><i class="pt-2 fs-5 fa fa-phone"></i
                        ></a>
                        <a href="" class="d-inline-block px-4 text-white bg-success"
                        ><i class="pt-2 fs-5 fa-brands fa-whatsapp"></i
                        ></a>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3">
                        <label class="labelStyle" class="" for="phone2"> الهاتف </label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1"
                        name="phone2"
                        id="phone2"
                        />
                        <a href="" class="d-inline-block px-4 text-white bg-primary"
                        ><i class="pt-2 fs-5 fa fa-phone"></i
                        ></a>
                        <a href="" class="d-inline-block px-4 text-white bg-success"
                        ><i class="pt-2 fs-5 fa-brands fa-whatsapp"></i
                        ></a>
                    </div>
                    <hr />
                    <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="alfkhd">فخذ </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="alfkhd"
                            id="alfkhdd"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="alfraa">فرع </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="alfraaa"
                            id="alfraaa"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="albtn">بطن </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="albtnn"
                            id="albtnn"
                            value=""
                                            />
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="codd1">كود1 </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="codd1"
                            id="codd1"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="codd2">كود2 </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="codd2"
                            id="codd2"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="codd3">كود3 </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="codd3"
                            id="codd3"
                            value=""
                                />
                        </div>
                    </div>
                    <hr />

                    <div class="d-flex align-items-center mb-1 mb-3">
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="type">الجنس</label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="type"
                            id="typeee"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="work"> المهنة </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="work"
                            id="work"
                            value=""
                                            />
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-1 mb-3">
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="yearOfBirth">الميلاد</label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="yearOfBirth"
                            id="yearOfBirth"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="age"> العمر </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="age"
                            id="age"
                            value=""
                                            />
                        </div>
                    </div>

                    <div class="d-flex mb-1 align-items-center">
                        <label class="labelStyle" class="" for="address">العنوان</label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1"
                        name="address"
                        id="address"
                        value=""
                                        />
                    </div>
                    <hr />

                    <div class="d-flex align-items-center mb-1 mb-3">
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="circle">الدائرة</label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="circle"
                            id="circle"
                            value="6"
                            min="1"
                        />
                        </div>
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="almrgaa"> المرجع </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="almrgaa"
                            id="almrgaaa"
                            value=""
                            min="0"
                        />
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3 widthOn3">
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="character">حرف </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="character"
                            id="character"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="userTable">جدول </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="userTable"
                            id="userTable"
                            value=""
                                            />
                        </div>
                        <div class="d-flex align-items-center mb-1">
                        <label class="labelStyle" class="" for="registe">قيد </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="registe"
                            id="registe"
                            value=""
                                            />
                        </div>
                    </div>
                    <div
                        class="d-flex mb-1 align-items-center justify-content-between"
                    >
                        <label class="labelStyle" class="" for="committee">لجنة</label>
                        <input
                        type="text"
                        readonly
                        class="form-control py-1 w-25 ms-2"
                        name="committee"
                        id="committee"
                        value="19"
                        min="1"
                        />
                        <input
                        type="text"
                        readonly
                        class="form-control py-1 w-75"
                        name="school"
                        id="school"
                        value="مدرسة الفردوس المتوسطة بنات"
                                        />
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-3">
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="registeNumber">تاريخ القيد</label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="registeNumber"
                            id="registeNumber"
                            value="6"
                            min="1"
                        />
                        </div>
                        <div class="d-flex align-items-center mb-1 w-50">
                        <label class="labelStyle" class="" for="lastCircle"> الدائرة السابقة </label>
                        <input
                            type="text"
                            readonly
                            class="form-control py-1"
                            name="lastCircle"
                            id="lastCircle"
                            value="4651365463"
                            min="0"
                        />
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table overflow-hidden rtl">
                        <thead
                            class="table-secondary text-center border-0 border-dark border-bottom border-2"
                        >
                            <tr>
                            <th>المتعهدين بالناخب</th>
                            <th>مدخلات المتعهد بالناخب</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider" id="mot3deen_table">


                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                </div>
            </div>

        @include('dashboard.contractors.partials.contractor-data-modal')

    </div>

@endsection


@push('js')
    <script>
        const voters = @json($voters);
        const chunkSize = 20; // Number of voters to load at a time
        let currentChunkIndex = 0; // Tracks the current chunk

        document.addEventListener("DOMContentLoaded", function () {
            const tbody = document.getElementById("userData");

            // Function to format date and time
            const formatDate = (isoDate) => {
                const date = new Date(isoDate);
                return new Intl.DateTimeFormat("ar-EG", {
                    dateStyle: "full",
                    timeStyle: "short",
                }).format(date);
            };

            // Function to load a chunk of voters
            const loadChunk = () => {
                const start = currentChunkIndex * chunkSize;
                const end = start + chunkSize;
                const voterChunk = voters.slice(start, end);

                voterChunk.forEach((voter, i) => {
                    const contractors = voter.contractors || []; // Ensure it's an array

                    // First row for the voter
                    const mainRow = document.createElement("tr");
                    mainRow.classList.add("border-bottom", "border-dark", "px-1");
                    mainRow.innerHTML = `
                    <input type="hidden" name="" id="voter_id" value="${voter.id}">
                    <td class="px-1" rowspan="${contractors.length + 1}">${start + i + 1}</td>
                    <td rowspan="${contractors.length + 1}" class="modalButton" data-bs-toggle="modal" data-bs-target="#madameen">${voter.name}
                        ${contractors.length > 1 ? `<br><span>المتعهدين : ${contractors.length}</span>` : ""}
                    </td>
                `;
                    tbody.appendChild(mainRow);

                    // Rows for each contractor
                    contractors.forEach((contractor, cIndex) => {
                        const contractorRow = document.createElement("tr");
                        contractorRow.classList.add("border-bottom", "border-dark");
                        contractorRow.style.color = "#000";

                        contractorRow.innerHTML = `
                        <td class="d-none" id="user_id">${contractor.id}</td>
                        <input type="hidden" id="conUrl" data-url="/con-profile/${contractor.token}">
                        <td class="bg-warning bg-opacity-25 w30">${voter.phone1 || "N/A"}</td>
                        <td class="bg-warning bg-opacity-25">${contractor.trust}%</td>
                        <td class="bg-warning bg-opacity-25" data-bs-toggle="modal" data-bs-target="#mota3ahdeenDataModern">${contractor.name}</td>
                        <td class="bg-warning bg-opacity-25 w30">${contractor.voters ? contractor.voters.length : 0}</td>
                        <td class="bg-warning bg-opacity-25">${(contractor.pivot && contractor.pivot.percentage !== null && contractor.pivot.percentage !== '' && contractor.pivot.percentage !== 'null') ? contractor.pivot.percentage : "0"}%</td>
                        <td class="bg-warning bg-opacity-25">${contractor.phone}</td>
                        ${
                            cIndex === 0
                                ? `<td rowspan="${contractors.length}">
                                    ${
                                    voter.status
                                        ? `<i class="fa fa-check-square text-success"></i>
                                            <span>تم التصويت</span>
                                            <span class="currentTime">${formatDate(voter.updated_at)}</span>`
                                        : `<span class="currentTime">${voter.committee ? voter.committee.name : "لايوجد"}</span>`
                                }
                                </td>`
                                : ""
                        }
                    `;
                        tbody.appendChild(contractorRow);
                    });
                });

                currentChunkIndex++;
            };

            // Initial load
            loadChunk();

            // Add scroll listener for lazy loading
            window.addEventListener("scroll", () => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
                    if (currentChunkIndex * chunkSize < voters.length) {
                        loadChunk();
                    }
                }
            });
        });
    </script>

    <script>
let url
    $("#addMota3ahed").on('change',function(){
        console.log($("#addMota3ahed").val());
        if($("#addMota3ahed").val() == "delete"){
            url = "/delete/mad"
        }else{
             url = "/ass/"+$(this).val()
        }

    $("#form-attach").attr("action", url )
})
</script>
@endpush

@push('js')

<script>
    $('td[data-bs-target="#mota3ahdeenDataModern"]').on("click", function () {
    // var id=$("#user_id").val();
    var id=$(this).siblings("#user_id").text();
    var conUrl=$(this).siblings("#conUrl").data('url');
    var url = '/con/' + id ;
    let cartona = "";
    let resultSearchDate = document.getElementById("voters_con");



    axios.get(url)
            .then(function (response) {
                console.log('Success:', response.data.user);
                $("#last_id").val(response.data.user.id)
            $("#nameMota3ahed").val(response.data.user.name)
            $("#phoneMota3ahed").val(response.data.user.phone)
            $("#trustingRate").val(response.data.user.trust)
            $("#trustText").text(response.data.user.trust)
            $("#noteMota3ahed").val(response.data.user.note)
            $("#changparent_id").val(response.data.user.parent)
            let voters= response.data.user.voters
            $("#con-url").text(conUrl)
            $('#delete-con').val(response.data.user.id)
            $("#RedirectLink").attr('href', conUrl);
            $('#copyConUrlBtn').attr('data-copy-url', conUrl);
            $('#voters_numberss').text(" "+response.data.user.voters.length + " ")
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

            let message= $('#message').val()
            document.getElementById('whatsapp-link').href = `https://wa.me/965${phoneNumber}?text=${encodeURIComponent(message)}%0A%0A${encodeURIComponent(conUrl)}`;

            $("#phone_wa").on("change", function() {
                let phone = $(this).val();
                let updatedMessage = $("#message").val(); // Update message if changed
                document.getElementById('whatsapp-link').href = `https://wa.me/965${phone}?text=${encodeURIComponent(updatedMessage)}%0A%0A${encodeURIComponent(conUrl)}`;
            });
            $('#delete-con').on('click',function(){
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


            })
            for (let i = 0; i < voters.length; i++) {
                var id = voters[i].id;
                cartona += `  <tr>
                            <td>
                              <input
                                type="checkbox"
                                class="check"
                                name="voter[]"
                                value="${id}"
                              />
                            </td>
                            <td>${id}</td>
                            <td>
                                ${voters[i].name}
                            </td>

                            <td>${(voters[i].pivot && voters[i].pivot.percentage !== null && voters[i].pivot.percentage !== '' && voters[i].pivot.percentage !== 'null') ? voters[i].pivot.percentage : "0"}%</td>
                            <td>${voters[i].phone1}</td>
                            <td></td>
                          </tr>
                        `;

            }

             resultSearchDate.innerHTML = cartona;

                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert(error.response.data.message);
            });
            $('[data-bs-target="edit-mot"]').on('change', function() {
                var elementName = $(this).attr('name') || $(this).prop('tagName').toLowerCase();
                var elementValue = $(this).val();
                var api = '/dashboard/mot-up/' + id ;
        var data = {
            name: elementName,
            value: elementValue
        };
        axios.post(api, data)
            .then(function (response) {
                console.log('Success:', response);
                alert(response.data.message)
                    // window.location.reload();

                        })
            .catch(function (error) {
                console.error('Error:', error);
                alert(error.response.data.message);
            });

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

// Attach a single event listener to the submit button
$("#submit-form").on("click", function (e) {
    // Prevent default form submission
    e.preventDefault();

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
        // Show confirmation and reload page if confirmed
        if (confirm(response.data.message)) {
            window.location.reload();
        }
    })
    .catch(function (error) {
        console.error('Error:', error);
        alert(error.response.data.message);
    });
});

$('td[data-bs-target="#madameen"]').on("click", function () {

let id=$(this).siblings("#voter_id").val();
var url = '/voter/' + id ;
axios.get(url)
.then(function (response) {
    console.log('Success:', response);
    $("#id").val(response.data.voter.alrkm_almd_yn)
    $("#name").val(response.data.voter.name)
    $("#family").val(response.data.family)
    $("#phone1").val(response.data.voter.phone1)
    $("#phone2").val(response.data.voter.phone2)
    $("#alfkhdd").val(response.data.voter.alfkhd)
    $("#alfraaa").val(response.data.voter.alfraa)
    $("#albtnn").val(response.data.voter.albtn)
    $("#codd1").val(response.data.voter.cod1)
    $("#codd2").val(response.data.voter.cod2)
    $("#codd3").val(response.data.voter.cod3)
    $("#typeee").val(response.data.voter.type)
    $("#almrgaaa").val(response.data.voter.almrgaa)
    $("#yearOfBirth").val(response.data.voter.yearOfBirth)
    $("#committee").val(response.data.committee)
    $("#school").val(response.data.school)
    let contractors=response.data.contractors;
    var cartona = [];
for (let i = 0; i < contractors.length; i++) {
cartona.push(`
<tr>
<td rowspan="3">${contractors[i].name}</td>
<td>الالتزام: ${(contractors[i].pivot && contractors[i].pivot.percentage !== null && contractors[i].pivot.percentage !== '' && contractors[i].pivot.percentage !== 'null') ? contractors[i].pivot.percentage : "0"}%</td>
</tr>
<tr>
<td>الهاتف: ${contractors[i].phone ? contractors[i].phone : "لايوجد"}</td>
</tr>
<tr>
<td>الملاحظات: ${contractors[i].note ? contractors[i].note : "لايوجد" } </td>
</tr>
`);
}
document.getElementById("mot3deen_table").innerHTML = cartona.join('');
    console.log(contractors.length);

            })
.catch(function (error) {
    console.error('Error:', error);
    alert('Failed to set votes.');
});


});
$("#addMota3ahed").on('change',function(){
let url = "/ass/"+$(this).val()
$("#form-attach").attr("action", url )
})

$(document).off('click.copyConUrl', '#copyConUrlBtn').on('click.copyConUrl', '#copyConUrlBtn', async function (event) {
    event.preventDefault();
    event.stopPropagation();
    if (event.stopImmediatePropagation) event.stopImmediatePropagation();

    var btn = this;
    var $btn = $(btn);
    var textNode = document.getElementById('copyConUrlText');
    var rawUrl = ($btn.attr('data-copy-url') || $('#con-url').text() || '').trim();

    if (!rawUrl) {
        if (window.toastr) {
            toastr.error('لا يوجد رابط لنسخه');
        } else {
            alert('لا يوجد رابط لنسخه');
        }
        return;
    }

    var absoluteUrl = rawUrl;
    if (!/^https?:\/\//i.test(rawUrl)) {
        absoluteUrl = window.location.origin.replace(/\/$/, '') + '/' + rawUrl.replace(/^\/+/, '');
    }

    $btn.addClass('is-copying');
    btn.disabled = true;
    if (textNode) textNode.textContent = 'جارٍ النسخ...';

    var copied = false;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(absoluteUrl);
            copied = true;
        } else {
            var tempInput = document.createElement('input');
            tempInput.value = absoluteUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999);
            copied = document.execCommand('copy');
            document.body.removeChild(tempInput);
        }
    } catch (error) {
        copied = false;
    }

    if (copied) {
        $btn.addClass('is-copied');
        if (textNode) textNode.textContent = 'تم النسخ ✓';
        if (window.toastr) toastr.success('تم نسخ الرابط');
        setTimeout(function () {
            $btn.removeClass('is-copied');
            if (textNode) textNode.textContent = 'نسخ الرابط';
        }, 1100);
    } else {
        if (textNode) textNode.textContent = 'فشل النسخ';
        if (window.toastr) {
            toastr.error('تعذر نسخ الرابط');
        } else {
            alert('تعذر نسخ الرابط');
        }
        setTimeout(function () {
            if (textNode) textNode.textContent = 'نسخ الرابط';
        }, 1100);
    }

    btn.disabled = false;
    $btn.removeClass('is-copying');
});
</script>

<script>
$(document).ready(function() {
$('.button').on('click', function() {
var buttonValue = $(this).val();
$('#type').val(buttonValue);

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
    }).appendTo('#export');
});


var form = $('#export')[0]; // Ensure we get the DOM element, not jQuery object
var formData = new FormData(form);
var submitBtn = $(this);
axios.get(form.action, formData)
    .then((res) => {
        console.log(res);
        setTimeout(() => {
            console.log(res.data);
        }, 2000);
        if (res.data.Redirect_Url) {
            window.location.href = res.data.Redirect_Url;
        } else {
            toastr.success(res.data.success);
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.log(error);
        toastr.error(error.response.data.error ?? '{{ __('main.unexpected-error') }}');
        submitBtn.attr('disabled', false);
    });
});
});






</script>

<script>
    $(document).ready(function() {
        $('#load-more').on('click', function() {
            var nextPage = this.getAttribute('data-next-page');
        var loadMoreButton = this;

            if (nextPage) {
                var $loadMoreBtn = $('#load-more');
                $loadMoreBtn.addClass('loading').prop('disabled', true);  // Add loading class and disable button

                $.ajax({
                    url: "{{ route('voters.load-more') }}?page=" + nextPage,
                    type: 'GET',
                    success: function(response) {
                        var $newRows = $(response.html);
                        $newRows.hide();  // Hide new rows initially
                        $('#userData').append($newRows);  // Append the new rows to the voter list
                        $newRows.fadeIn(800);  // Fade in the new rows for a smooth effect

                        nextPage++;
                        loadMoreButton.setAttribute('data-next-page', nextPage);

                        if (!response.hasMorePages) {
                            loadMoreButton.style.display = 'none';
                        }
                        $loadMoreBtn.removeClass('loading').prop('disabled', false).text('المزيد');  // Remove loading class, re-enable button, and reset text
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                        $loadMoreBtn.removeClass('loading').prop('disabled', false).text('المزيد');  // Remove loading class and re-enable button on error
                    }
                });
            }
        });
    });
    </script>

@endpush
