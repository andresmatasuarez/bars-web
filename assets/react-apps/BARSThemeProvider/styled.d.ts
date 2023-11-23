import 'styled-components';
import { BARSTheme } from './theme';

declare module 'styled-components' {
  export interface DefaultTheme extends BARSTheme {}
}
