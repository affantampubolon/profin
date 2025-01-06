import { resolve, basename, join } from "path";
import MiniCssExtractPlugin, {
  loader as _loader,
} from "mini-css-extract-plugin";
import HtmlWebpackPlugin from "html-webpack-plugin";
import { readdirSync, statSync } from "fs";

// Function to get all SCSS files in the directory
function getSCSSEntries() {
  const scssFolder = resolve(__dirname, "assets/scss");
  const entries = {};

  readdirSync(scssFolder).forEach((file) => {
    if (file.endsWith(".scss")) {
      const name = basename(file, ".scss");
      entries[name] = resolve(scssFolder, file);
    }
  });
  return entries;
}

// Function to generate HTML plugins from Pug templates
function generateHTMLPlugins() {
  const htmlFolder = resolve(__dirname, "assets/pug/pages/template");
  const plugins = [];

  // Recursive function to traverse directories
  function traverseDirectory(dir) {
    const files = readdirSync(dir);

    files.forEach((file) => {
      const filePath = join(dir, file);
      const stat = statSync(filePath);

      if (stat.isDirectory()) {
        traverseDirectory(filePath); // Recursively traverse directories
      } else if (file.endsWith(".pug")) {
        const name = basename(file, ".pug");
        plugins.push(
          new HtmlWebpackPlugin({
            template: filePath,
            filename: resolve(__dirname, "template", `${name}.html`), // Updated path
            inject: false,
          })
        );
      }
    });
  }

  traverseDirectory(htmlFolder);
  return plugins;
}

export const mode = "development";
export const entry = {
  ...getSCSSEntries(),
};
export const output = {
  path: resolve(__dirname, "assets/css/dist"),
  clean: true,
};
export const stats = {
  children: true,
};
export const module = {
  rules: [
    {
      test: /\.scss$/,
      use: [
        _loader,
        "css-loader",
        {
          loader: "sass-loader",
          options: {
            implementation: require("sass"),
          },
        },
      ],
    },
    {
      test: /\.pug$/,
      use: "pug-loader",
    },
  ],
};
export const plugins = [
  new MiniCssExtractPlugin({
    filename: "[name].css", // Output CSS to assets/css folder
  }),
  ...generateHTMLPlugins(),
];
export const devServer = {
  static: {
    directory: join(__dirname, "assets/css/dist"),
  },
  port: 8080,
};
