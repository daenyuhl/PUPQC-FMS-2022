
<script>
    $(document).ready(function(){

        // GLOBAL VARIABLE
        var APP_URL = {!! json_encode(url('/')) !!}
        var API_TOKEN = localStorage.getItem("API_TOKEN")
        var USER_DATA = localStorage.getItem("USER_DATA")
        var BASE_API = APP_URL + '/api/v1/observation/'
        var SCHEDULE_ID = "{{ $schedule_id }}"
        var CLASS_SCHEDULE_ID
        var CLASS_SCHEDULE_DATA
        var FACULTY_ID
        // END OF GLOBAL VARIABLE

        let class_schedule = class_schedule_response.data

        $.each(class_schedule, function(i, value){
            if(class_schedule[i].id == SCHEDULE_ID){
                CLASS_SCHEDULE_DATA = class_schedule[i]
            }
        })

        function getSchedule(){

            let data = CLASS_SCHEDULE_DATA
            let user_role = ""
            console.log(data)

            $.each(data.faculty.user.user_role, function(i){
                if(i < (data.faculty.user.user_role.length) - 1){
                    user_role += data.faculty.user.user_role[i].role.title + ', '
                }
                else{
                    user_role += data.faculty.user.user_role[i].role.title
                }
            })
            
            $('#class_schedule_id').val(data.id)
            FACULTY_ID = data.faculty.id

            // ROOM DETAILS
            $('#room_building').html(data.room.building)
            $('#room_floor').html(data.room.floor)
            $('#room_status').html(data.room.status)
            $('#room_number').html(data.room.room_number)
            $('#room_type').html(data.room.room_type)

            // SUBJECT DETAILS
            $('#assignment_code').html(data.assignment_code)
            $('#subject_code').html(data.subject_code)
            $('#subject_schedule').html(data.day_time)
            $('#subject_status').html(data.subject_offering.status)
            $('#teaching_hours').html(data.subject_offering.teaching_hours)
            $('#subject_description').html(data.subject_offering.curriculum_subject.subject.title)

            // ROOM DETAILS MODAL
            $('#room_building_modal').html(data.room.building)
            $('#room_floor_modal').html(data.room.floor)
            $('#room_status_modal').html(data.room.status)
            $('#room_number_modal').html(data.room.room_number)
            $('#room_type_modal').html(data.room.room_type)

            // SUBJECT DETAILS MODAL
            $('#assignment_code_modal').html(data.assignment_code)
            $('#subject_code_modal').html(data.subject_code)
            $('#subject_schedule_modal').html(data.day_time)
            $('#subject_status_modal').html(data.subject_offering.status)
            $('#teaching_hours_modal').html(data.subject_offering.teaching_hours)
            $('#subject_description_modal').html(data.subject_offering.curriculum_subject.subject.title)
            
            // FACULTY DETAILS
            $('#faculty_image').html(APP_URL + "/" + data.faculty.image)
            $('#faculty_name').html(data.faculty.full_name)
            $('#faculty_type').html(data.faculty.faculty_type.title)
            $('#faculty_role').html(user_role)
            $('#faculty_academic_rank').html(data.faculty.academic_rank.title)
            $('#faculty_designation').html(data.faculty.designation.title)
            $('#faculty_specialization').html(data.faculty.specialization.title)
            $('#faculty_program').html(data.faculty.program.title)

            // FACULTY DETAILS
            $('#faculty_image_modal').html(APP_URL + "/" + data.faculty.image)
            $('#faculty_name_modal').html(data.faculty.full_name)
            $('#faculty_type_modal').html(data.faculty.faculty_type.title)
            $('#faculty_role_modal').html(user_role)
            $('#faculty_academic_rank_modal').html(data.faculty.academic_rank.title)
            $('#faculty_designation_modal').html(data.faculty.designation.title)
            $('#faculty_specialization_modal').html(data.faculty.specialization.title)
            $('#faculty_program_modal').html(data.faculty.program.title)

            $('#start_time_input').val(data.start_time)
            $('#end_time_input').val(data.end_time)

            let dayEnabled
            if(data.day == "Sunday"){dayEnabled = 0}
            else if(data.day == "Monday"){dayEnabled = 1}
            else if(data.day == "Tuesday"){dayEnabled = 2}
            else if(data.day == "Wednesday"){dayEnabled = 3}
            else if(data.day == "Thursday"){dayEnabled = 4}
            else if(data.day == "Friday"){dayEnabled = 5}
            else if(data.day == "Saturday"){dayEnabled = 6}

            console.log(moment().format('L'))
            let today = moment().format('YYYY-MM-DD')
            let time = data.end_time


            $("#date_of_observation").flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d" + ' ' + data.start_time,
                minDate: moment(today + ' ' + data.end_time).format('YYYY-MM-DD HH:MM'),
                "disable": [
                    function(date) {
                        return (date.getDay() != dayEnabled);  // disable weekends
                    }
                ]
            });

            removeLoader()
        }



        getSchedule()
        
        function getActivity(){
            $.ajax({
                url: APP_URL+"/api/v1/activity/"+ACTIVITY_ID,
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "Authorization": API_TOKEN,
                    "Content-Type": "application/json"
                },

                success: function(data){

                    console.log(data)

                    date = moment(new Date).format()

                    let created_at = moment(data.created_at).format('LLL');
                    let status = (data.deleted_at === null) ? 'Active' : 'Inactive';
                    let is_required_view = ""

                    $('#id_view').html(data.id);
                    $('#title').html(data.title);
                    $('#description').html(data.description);
                    $('#start_time').html(moment(data.start_datetime).format('LLL'));
                    $('#end_time').html(moment(data.end_datetime).format('LLL'));
                    $('#act_type').html(data.activity_type.title); 
                    $('#created_by').html(data.created_by_user.faculty.first_name); 
                    console.log(data.activity_type)  

                    if(data.is_required == 0){
                        is_required_view = "Not required to attend"
                    } else{
                        is_required_view = "Required to attend"
                    }

                    if(moment(data.start_datetime).format() < date && moment(data.end_datetime).format() > date){
                        $('#status').html(`<span class="badge badge-warning">
                                <span>Ongoing</span>
                                </span>`);
                    }
                    else if(date > moment(data.end_datetime).format()){
                        $('#status').html(`<span class="badge badge-warning">
                                <span>Ended</span>
                                </span>`);
                        //$('#status').addClass('text-success');
                    }
                    else{
                        $('#status').html(`<span class="badge badge-warning">
                                <span>Pending</span>
                                </span>`);
                        //$('#status').addClass('text-warning');
                    }
                    
                    
                    $('#is_required').html(is_required_view);
                    $('#location').html(data.location);

                    //$('#created_at_view').html(created_at);

                    // console.log(data.memorandum_file_directory)
                    //document.getElementById("memorandum_view").src=APP_URL + data.memorandum_file_directory;
                    //$('#memorandum_view').src("{{ asset('" + data.memorandum_file_directory + "') }}")

                    if(data.memorandum_file_directory == "NA"){
                        $('#if_memo').html("<span>No Memorandum uploaded</span>")
                    }
                    else{
                        $('#if_memo').html('<div class="embed-responsive embed-responsive-16by9">'
                        +'<iframe id="memo" class="embed-responsive-item" src="..."></iframe></div>')
                        
                        document.getElementById("memo").src=APP_URL + "/" +data.memorandum_file_directory;
                    }
                }
            // ajax closing tag
            })
        }

        // getActivity()

        function requiredFacultyDatatable(){
            requiredFacultyDatatable = $('#requiredFacultyDatatable').DataTable({
                "ajax": {
                    url: ATTENDANCE_API+"search/"+ACTIVITY_ID,
                    dataSrc: ''
                },
                "columns": [
                    { data: "id"},
                    { data: "created_at"},
                    { data: "deleted_at"},
                    { data: "faculty.first_name", render: function(data, type, row){
                        let html = ''
                        html += row.faculty.first_name + ' ' + row.faculty.last_name
                        return html
                    }},
                    { data: "status", render: function(data, type, row){
                        if (moment(row.start_datetime).format() < date && moment(row.end_datetime).format() > date){
                            return "Ongoing"
                        }
                        else if (date > moment(row.end_datetime).format() ){
                            return "Ended"
                        }
                        else{
                            return data
                        }
                    }},
                    { data: "time_in", render: function(data, type, row){
                        if (data == null){
                            return "Not yet"
                        }
                        else{
                            return moment(data).format('LLL')
                        }
                    }},
                    { data: "time_out", render: function(data, type, row){
                        if (data == null){
                            return "Not yet"
                        }
                        else{
                            return moment(data).format('LLL')
                        }
                    }},
                    { data: "id", render: function(data, type, row){
                        return `</div>
                                    <button type="button" class="btn btn-sm btn-success btnViewDetails" id="${row.id}">
                                    <div>Check Uploaded Files</div>
                                </button>`
                    }}
                ],
                "aoColumnDefs": [{ "bVisible": false, "aTargets": [0, 1, 2] }],
                "order": [[1, "desc"]]
                });
        };

        // requiredFacultyDatatable()

        // REFRESH DATATABLE FUNCTION
        function refresh(){
            let url = ATTENDANCE_API+'search/' + ACTIVITY_ID;

            requiredFacultyDatatable.ajax.url(url).load()
        }
        // REFRESH DATATABLE FUNCTION
        

        $('#btnEditRequiredFaculty').on('click', function(){
            let form_url = ATTENDANCE_API + "unrequired_faculty/" + ACTIVITY_ID

            $('#requiredFacultyDatatableModal').DataTable().destroy()
            requiredFacultyDatatableModal = $('#requiredFacultyDatatableModal').DataTable({
                "ajax": {
                    url: form_url,
                    dataSrc: ""
                },
                "async": true,
                "columns": [
                    { data: "id"},
                    { data: "created_at"},
                    { data: "user.email", render: function(data, type, row){
                        return data
                    }},
                    { data: "first_name", render: function(data, type, row){
                        let html = ''
                        html += row.first_name + ' ' + row.last_name
                        return html
                    }},
                    { data: "id", render: function(data, type, row){
                        return `<div class="custom-control custom-switch">
                                    <input type="checkbox" name="faculty_required[]" class="custom-control-input faculty_status" id="${row.id}" value="${row.id}">
                                    <label id="status_label" class="custom-control-label" for="${row.id}">Yes</label>
                                </div>`
                    }}
                ],
                "aoColumnDefs": [{ "bVisible": false, "aTargets": [0, 1] }],
                "order": [[1, "desc"]],
                "bPaginate": false,
            });

            $('#editRequiredFacultyModal').modal('show');
        })

        // $(document).on("click", ".faculty_status", function(){
        //     Swal.fire({
        //         icon: 'warning',
        //         text: 'This feature is still under development'
        //     })
        // });


        $('#updateRequiredFacultyForm').on('submit', function(e){
            e.preventDefault()
            // Swal.fire({
            //     icon: 'warning',
            //     text: 'This feature is still under development'
            // })

            let required_faculty = $("input[name='faculty_required[]']:checked")
              .map(function(){
                return {
                "activity_id": ACTIVITY_ID,
                "faculty_id": $(this).val()
                }
            }).get();

            let form_url = ATTENDANCE_API+"multi_insert"


             // ajax opening tag
             $.ajax({
                    url: form_url,
                    method: "POST",
                    data: JSON.stringify(required_faculty),
                    dataType: "JSON",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": API_TOKEN,
                        "Content-Type": "application/json"
                    },
                    success: function(data){
                        console.log(data)
                        notification('success', 'Required Faculty')
                        $('#editRequiredFacultyModal').modal('hide');
                        refresh()
                    },
                    error: function(error){
                        $.each(error.responseJSON.errors, function(key,value) {
                            swalAlert('warning', value)
                        });
                        console.log(error)
                        console.log(`message: ${error.responseJSON.message}`)
                        console.log(`status: ${error.status}`)
                    }
                // ajax closing tag
            })
        })
        

        $(document).on("click", ".btnViewDetails", function(){
            var id = this.id
            
            $.ajax({
                url: APP_URL+'/api/v1/activity_submitted_proof/' + id,
                type: "GET",
                dataType: "JSON",
                success: function (data){   
                    console.log(data)

                    let html = `<li class="list-group-item d-flex justify-content-between" disabled="">
                                                    <span class="text-primary"><strong>Submitted File/s</strong></span>
                                                    <span class="text-primary"><strong>Date Submitted</strong></span>
                                                </li>`

                    // let header = `<h5 class="text-dark">Faculty: ${data.faculty.last_name}, ${data.faculty.first_name}</h5>`

                    // IF FACULTY DOES NOT HAVE ANY SUBMITTED REQUIREMENTS YET
                    if(data.length == 0){
                        html += `&nbsp;<div class="text-center">Empty Submission
                            </div>`
                    }

                    // PASSING SUBMITTED REQUIREMENTS DATA INTO HTML VAR TO PASS IT TO DOM
                    $.each(data, function(i){
                        let file_id = data[i].id
                        let file_link_directory = APP_URL + "/" + data[i].file_link_directory
                        let file_name = data[i].file_name
                        let date_submitted = data[i].date_submitted

                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <button class="btn btn-info" onclick="window.open('${file_link_directory}')" target="_blank">${file_name}</button>
                                    
                                    <span class="badge badge-light">${moment(date_submitted).format('lll')}</span>
                                 </li>`
                    })

                    $.ajax({
                        url: ATTENDANCE_API + id,
                        method: "GET",
                        dataType: "JSON",
                        headers: {
                            "Accept": "application/json",
                            "Authorization": API_TOKEN,
                            "Content-Type": "application/json"
                        },

                        success: function(data){
                            console.log(data)
                            $("#attendance_id").val(data.id);


                            if(data.proof_of_attendance_file_link == null){
                                $("#proof_link_view").val("No URL");
                            }
                            else{
                                $("#proof_link_view").val(data.proof_of_attendance_file_link);
                            }

                            if(data.remarks == null){
                                $("#proof_remarks").html("No Remarks");
                            }
                            else{
                                $("#proof_remarks").val(data.remarks);
                            }

                            $("#proof_status").val(data.status);
                            
                        },
                        error: function(error){
                            $.each(error.responseJSON.errors, function(key,value) {
                                swalAlert('warning', value)
                            });
                            console.log(error)
                            console.log(`message: ${error.responseJSON.message}`)
                            console.log(`status: ${error.status}`)
                        }
                    // ajax closing tag
                    })
                    
                    //$('#fileModalHeader').html(header)
                    $('#fileModalBody').html(html)
                    $('#fileViewerModal').modal('show')
                },
            });
        });

        $('.btnSubmittedUpdate').on('click', function(e){
            
            let id = $('#attendance_id').val()
            let status = $('#proof_status').val()
            let remarks = $('#proof_remarks').val()

            let form_url = ATTENDANCE_API + id
            let form_data = {
                "status": status,
                "remarks": remarks
            }

            console.log(form_data)
            $.ajax({
                    url: form_url,
                    method: "PUT",
                    data: JSON.stringify(form_data),
                    dataType: "JSON",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": API_TOKEN,
                        "Content-Type": "application/json"
                    },
                    success: function(data){
                        console.log(data)
                        notification('info', 'Submitted Requirement/s')
                        $('#fileViewerModal').modal('hide');
                        refresh()
                    },
                    error: function(error){
                        $.each(error.responseJSON.errors, function(key,value) {
                            swalAlert('warning', value)
                        });
                        console.log(error)
                        console.log(`message: ${error.responseJSON.message}`)
                        console.log(`status: ${error.status}`)
                    }
                // ajax closing tag
            })
        })


        $('.btnSetObservation').on('click', function(){
            console.log(SCHEDULE_ID)
            let data = CLASS_SCHEDULE_DATA;

            $('#observationModal').modal('show')
        })

        $('.btnSubmitObservation').on('click', function(e){
            e.preventDefault()

            var form_url = BASE_API
            var form = $("#setObservationForm").serializeArray();
            let data = {
                "class_schedule_id": $('#class_schedule_id').val(),
                "date_of_observation": $('#date_of_observation').val(),
                "faculty_id": FACULTY_ID,
            }

            // $.each(form, function(){
            //     data[[this.name]] = this.value;
            // })

            console.log(data)
            $.ajax({
                    url: form_url,
                    method: "POST",
                    data: JSON.stringify(data),
                    dataType: "JSON",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": API_TOKEN,
                        "Content-Type": "application/json"
                    },
                    success: function(data){
                        notification('success', 'Observation Schedule')
                        $("#setObservationForm").trigger("reset")
                        $("#observationModal").modal("hide")
                        
                        setInterval(() => {
                            window.location.href = APP_URL+'/acad_head/class_observation';
                        }, 1500);
                    },
                    error: function(error){
                        $.each(error.responseJSON.errors, function(key,value) {
                            swalAlert('warning', value)
                        });
                        console.log(error)
                        console.log(`message: ${error.responseJSON.message}`)
                        console.log(`status: ${error.status}`)
                    }
                // ajax closing tag
                })

        })

    });
</script>
