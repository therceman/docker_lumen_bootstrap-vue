<template>
  <router-view />
</template>

<script>
import './charts/ChartjsConfig';

export default {
  name: 'App',
  mounted() {
    console.log('app mounted');
    this.$store.dispatch('client/login');
  }
}
</script>

