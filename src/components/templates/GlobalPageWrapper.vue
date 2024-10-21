<template>
  <div class="container-fluid mt-4">
    <div class="row align-items-start">
      <h1 class="mb-3">WP Fedora</h1>
      <div class="fedora-menu">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" :class="{ isGeneralActive: active }" @click.prevent="" href="#">General</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" aria-disabled="true">Disabled</a>
          </li>
        </ul>
      </div>
      <div class="col-9">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
// NPM Modules

export default {
  name: 'GlobalPageWrapper',
  props: {
    navLinkDataProps: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      navLinkData: { ...this.navLinkDataProps }
    };
    emitData() {
      this.$emit('nav-link-clicked', { isGeneralActive: this.navLinkData.isGeneralActive });
    },
    hideAllPages() {
      this.navLinkData.isGeneralActive = false;
    },
    showGeneral() {
      this.hideAllPages();
      this.navLinkData.isGeneralActive = true;
      this.emitData();
    }
  }
};
</script>
