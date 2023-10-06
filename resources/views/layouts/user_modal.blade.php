<!-- user_modal -->
<div class="modal modal-common fade" id="user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content modal-g-photo">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body md-detail">
            <p class="p-require">* Indicates required</p>
            <form class="frm-details" id="user_form">
              @csrf
              <div class="frm-grp">
                  <label>First name*</label>
                  <input class="input-cstm" name="first_name" type="text" placeholder="First Name" value="{{ Auth::user()->first_name }}"/>
              </div>
              <div class="frm-grp">
                  <label>Last name*</label>
                  <input class="input-cstm" name="last_name" type="text" placeholder="Last Name" value="{{ Auth::user()->last_name }}" />
              </div>
              @if(auth()->user()->user_status==0)
              <div class="frm-grp">
                  <label>Medical registered number*</label>
                  <input class="input-cstm" name="medical_no" type="text" placeholder="" value="{{ Auth::user()->medical_no }}" />
              </div>
              @else
              <div class="frm-grp">
                <label>Hospital name*</label>
                <input class="input-cstm" name="hospital_name" type="text" placeholder="hospital Name" value="{{ Auth::user()->hospital_name }}" />
              </div>
              <div class="frm-grp">
                <label>Headline*</label>
                <input class="input-cstm" name="headline" type="text" placeholder="" value="{{ Auth::user()->headline }}" />
              </div>
              @endif
              <div class="frm-grp">
                <label>Speciality*</label>
                <input class="input-cstm" name="grade_primary" type="text" placeholder="" value="{{ Auth::user()->grade_primary }}" />
              </div>
              <div class="frm-grp">
                <label>Speciality Secondary</label>
                <input class="input-cstm grade_secondary" name="grade_secondary" type="text" placeholder="" value="{{ Auth::user()->grade_secondary }}" />
              </div>
              @if(auth()->user()->user_status==0)
              <div class="frm-grp">
                <label>Current Position</label>
                <select class="input-cstm current_position" name="current_position" id="current_position">
                  <option selected disabled class="input-cstm">Please Select</option>
                  @foreach ($user->experience as $experience)
                  @if(is_null($experience->end_date))
                  <option value={{ $experience->id }}>{{ $experience->position }} at {{ $experience->name }}</option>
                  @endif
                  @endforeach
                </select>
              </div>
              <div class="frm-grp">
                <label>Education</label>
                <select class="input-cstm education" name="education" id="education">
                  <option selected disabled class="input-cstm">Please Select</option>
                  @foreach ($user->education as $education)
                  <option value={{ $education->id }}>{{ $education->name }}</option>
                  @endforeach
                </select>
              </div>
              @endif
              <h4>Location</h4>
              <div class="frm-grp country_div">
                <label>Country/Region</label>
                <select class="js-example-basic-single form-select user_country" data-width="100%" id="user_country" name="country">
                  <option value="0"selected disabled hidden>select country</option>
                  @foreach (country() as $code=>$country)
                    <option value="{{ $country['name'] }}">{{ $country['code'] }}-{{ $country['name'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="frm-grp">
                  <label>City</label>
                  <input class="input-cstm city" name="city" type="text" placeholder=""/>
              </div>
              <div class="contact-info"><a href="#">Contact Info</a></div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-blue-modal submit_user">Apply</button>
        </div>
      </div>
    </div>
  </div>