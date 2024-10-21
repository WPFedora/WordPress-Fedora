<template>
  <GlobalPageWrapper :nav-link-data-props="navLinkData" @nav-link-clicked="handleNavLinkClicked">
    <GeneralSettings v-if="navLinkData.isGeneralActive" :general-settings-data-props="generalSettingsData" />
  </GlobalPageWrapper>
</template>

<script lang="ts">
// Custom Modules
import GeneralSettings from './components/pages/GeneralSettings.vue';
import GlobalPageWrapper from './components/templates/GlobalPageWrapper.vue';

export default {
  name: 'App',
  components: {
    GeneralSettings,
    GlobalPageWrapper
  },
  data() {
    return {
    };
  }
};
</script>
