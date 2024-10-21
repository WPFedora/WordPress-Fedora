<template>
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
