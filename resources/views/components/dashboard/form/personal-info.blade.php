<div class="card">
    <div class="card-body">

    <h3>Personal Info</h3>
    <x-dashboard.form.input-text error-key="name" name="name" id="name" label-title="Name"/>

    <x-dashboard.form.input-text error-key="email" name="email" id="email" label-title="Email"/>

    <x-dashboard.form.input-password error-key="password" name="password" id="password"
                                 label-title="Password"/>
    <x-dashboard.form.media title="Add Image" :images="old('gallery')"
    name="image" />

    <x-dashboard.form.input-text error-key="phone" name="phone" id="phone" label-title="Phone"/>

    <x-dashboard.form.input-select error-key="roles"
                                   :options="$relations['roles']"
                                   option-lable="name"
                                   track-by="id"
                                   :multible="true"
                                   name="roles[]" id="roles"
                                   label-title="Roles"/>




        <x-dashboard.form.submit-button/>
    </div>
</div>
