import { defineConfig } from 'vite';
import wordpress from 'wordpress-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
  plugins: [
    wordpress({
      input: [
        'src/scss/main.scss',
        'src/js/main.js',
      ],

      refresh: true,
    }),
    viteStaticCopy({
      targets: [
        {
          src: 'src/images/*.svg',
          dest: 'images',
        },
      ],
    }),
  ],
  server: {
    cors: true,
    port: 5173,
  },
  build: {
    rollupOptions: {
      output: {
        entryFileNames: `js/[name]-[hash].js`,
        chunkFileNames: `js/[name]-[hash].js`,
        assetFileNames: ({ name }) => {
          let extensionType = name.split('.').at(1);

          return `${extensionType}/[name]-[hash][extname]`;
        },
      },
    },
  },
});
