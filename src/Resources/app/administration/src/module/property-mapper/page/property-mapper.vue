<template>
  <sw-page>
    <sw-card title="Eigenschafts-Abgleich" class="sw-card">
      <p>Starte hier manuell den vollständigen Abgleich aller Produkteigenschaften.</p>

      <sw-button
          variant="primary"
          :loading="isLoading"
          @click="runSync"
          style="margin-top: 20px;"
      >
        Abgleich jetzt starten
      </sw-button>

      <p v-if="successMessage" style="margin-top: 20px;">
        ✅ {{ successMessage }}
      </p>
    </sw-card>
  </sw-page>
</template>

<script>
export default {
  name: 'property-mapper',

  data() {
    return {
      isLoading: false,
      successMessage: ''
    };
  },

  methods: {
    async runSync() {
      this.isLoading = true;
      this.successMessage = '';

      try {
        const response = await this.$http.post('/api/_action/property-mapper/run');
        const updated = response.data.updated ?? 0;
        this.successMessage = `Abgleich abgeschlossen. ${updated} Produkte wurden aktualisiert.`;
      } catch (error) {
        this.successMessage = '❌ Fehler beim Ausführen des Abgleichs.';
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.sw-card {
  max-width: 600px;
  margin: 40px auto;
}
</style>
