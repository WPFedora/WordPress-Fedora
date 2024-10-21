<template>
  <div id="general-settings-page" class="container-fluid ml-4">
    <h2 class="mb-4">General Settings</h2>
    <form novalidate @submit.prevent="submitHandler">
      <div class="form-check form-switch-custom">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked />
        <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
        <span class="slider"></span>
        <div class="form-text">
          Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special
          characters, or emoji.
        </div>
      </div>
      <div class="form-check form-switch-custom">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked />
        <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
        <span class="slider"></span>
        <div class="form-text">
          Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special
          characters, or emoji.
        </div>
      </div>
      <div class="form-check form-switch-custom">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked />
        <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
        <span class="slider"></span>
        <div class="form-text">
          Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special
          characters, or emoji.
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</template>

<script lang="ts">
export default {
  name: 'GeneralSettings',
  props: {
    generalSettingsDataProps: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      generalSettings: { ...this.generalSettingsDataProps }
    };
  },
  methods: {
    submitHandler() {}
  }
};
</script>
