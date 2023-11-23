import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import inject from "@rollup/plugin-inject";

export default defineConfig((mode) => ({
  // Fixes: ReferenceError: process is not defined
  define:
    mode.command === "build"
      ? { "process.env.NODE_ENV": "'production'" }
      : { "process.env.NODE_ENV": "'development'" },

  plugins: [
    // Expose jquery
    // Keep this as the first item in the plugins array
    // https://dev.to/chmich/setup-jquery-on-vite-598k
    inject({
      $: "jquery",
      jQuery: "jquery",
    }),

    react(),
  ],
  build: {
    chunkSizeWarningLimit: 1024,
    emptyOutDir: false,
    sourcemap: true,
    outDir: "./wp-content/themes/bars2013",

    lib: {
      name: "selection",
      entry: "./assets/react-apps/selection/index.tsx",
      fileName: "selection",
      formats: ["iife"],
    },
  },
}));
