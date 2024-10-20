<template>
  <GlobalPageWrapper>
    <h1>Hello WordPress with Vue 3!</h1>
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
