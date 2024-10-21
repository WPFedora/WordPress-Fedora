<template>
  <div id="general-settings-page" class="container-fluid ml-4">
    <h2 class="mb-4">General Settings</h2>
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
