{{-- Job Post Modal --}}
<div class="modal modal-common addmodal" id="job_modal" tabindex="-1" aria-labelledby="experienceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content modal-g-photo">
        <div class="modal-header">
          <h5 class="modal-title" id="jobModalLabel">Add Job</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body md-detail">
            <p class="p-require">* Indicates required</p>
            <form class="frm-details" id="job_post_form">
              @csrf
              <div class="mb-3">
                <input type="hidden" class="form-control job_post_modal_id" id="job_post_modal_id" name="id" value="{{ encryptid('0') }}">
              </div>
              <div class="frm-grp">
                  <label>Title<span style="color:red">*</label>
                  <input class="input-cstm title" type="text" name="title" placeholder="Please Enter Title" value=""/>
              </div>
  
              <div class="frm-grp">
                <label>Skills<span style="color:red">*</span></label>
               <select class=" form-select js-example-basic-multiple select_skills" name="skills[]" multiple="multiple" placeholder="Please Select Skill">
                @if($all_skills->isEmpty())
                  <option selected disabled value="0">First Enter Skills</option>
                @else
                  @foreach($all_skills as $skills)
                  <option  value="{{$skills->id}}">{{$skills->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
             
              <div class="frm-grp">
                  <label>Work Period<span style="color:red">*</label>
                  <input class="input-cstm work_period" name="work_period" type="text" placeholder="Please Enter Work Period"/>
              </div>
  
              <div class="frm-grp">
                <label>Experience<span style="color:red">*</span></label>
               <select class=" form-select js-example-basic-multiple select_experience" name="experience">
                <option value="1">Select Experience</option>
                <option value="Entery">Entery</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Expert">Expert</option>
              </select>
              </div>
              
              <div class="frm-grp">
                <label>Hourly Rate<span style="color:red">*</label>
                <input class="input-cstm hourly_rate" name="hourly_rate" type="text" placeholder="Please Enter Hourly Rate"/>
             </div>
  
             <div class="frm-grp">
              <label>Attach File</label>
              <input class="input-cstm attach_file" name="attach_file" type="file" />
           </div>
           <img class="attach_image" width="48" src="">

          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn  delete_job" data-id="0">Delete Job</button>
          <button type="button" class="btn btn-blue-modal submit_job">Submit</button>
        </div>
      </div>
    </div>
    </div>