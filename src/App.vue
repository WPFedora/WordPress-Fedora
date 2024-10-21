<template>
  <GlobalPageWrapper :nav-link-data-props="navLinkData" @nav-link-clicked="handleNavLinkClicked">
  </GlobalPageWrapper>
</template>

<script lang="ts">
// Custom Modules
import GlobalPageWrapper from './components/templates/GlobalPageWrapper.vue';

export default {
  name: 'App',
  components: {
    GlobalPageWrapper
  }
};
</script>
