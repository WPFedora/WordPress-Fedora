<template>
  <div id="general-settings-page" class="container-fluid ml-4">
    <h2 class="mb-4">General Settings</h2>
    <form novalidate @submit.prevent="submitHandler">
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
