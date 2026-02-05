import { faSpinner } from '@fortawesome/free-solid-svg-icons';
import fetchJsonp from 'fetch-jsonp';
import { useEffect, useState } from 'react';
import styled from 'styled-components';

import { DangerousHTML } from '../../App/commons';
import { Stylable } from '../../types';
import { FAIcon } from '../../utils';

type OembedVideoData = {
  /**
   * @example 'https://www.youtube.com/@Cmudfilms'
   * @example 'https://vimeo.com/user159095503'
   */
  author_url: string;

  /**
   * HTML iframe string
   * @example '<iframe width="200" height="113" src=......></iframe>'
   */
  html: string;

  author_name: 'Griff Furst';

  /**
   * @example 'YouTube'
   * @example 'Vimeo'
   */
  provider_name: string;

  /**
   * @example 'https://www.youtube.com/'
   * @example 'https://vimeo.com/'
   */
  provider_url: string;

  /**
   * @example 'https://i.ytimg.com/vi/RwbOlxl7CCg/hqdefault.jpg'
   * @example 'https://i.vimeocdn.com/video/1754687202-8fc0bcb8f3fbf535bc7124e36fe41c9a56334e664ef92b18b4abf552dc625241-d_295x166'
   */
  thumbnail_url: string;

  /**
   * @example 'https://www.youtube.com/watch?v=RwbOlxl7CCg'
   * @example 'https://vimeo.com/885238407'
   */
  url: string;
  title: string;
  type: 'video';
  version: '1.0';

  thumbnail_width: number;
  thumbnail_height: number;
  height: number;
  width: number;
};

const IframeWrapper = styled(DangerousHTML)`
  > iframe {
    width: 100% !important;
    height: auto !important;
    min-height: 300px !important;
  }
`;

const LoaderWrapper = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
`;

export default styled(function TrailerEmbed({
  className,
  url,
}: Stylable & {
  url: string;
}) {
  const [oembedData, setOembedData] = useState<OembedVideoData>();

  useEffect(() => {
    (async () => {
      const qs = new URLSearchParams();
      qs.append('url', url);

      /**
       * Use the [Noembed](https://noembed.com/) service as proxy to oembed calls
       * for a unified interface.
       */
      const result = await fetchJsonp(`https://noembed.com/embed?${qs.toString()}`);

      setOembedData((await result.json()) as unknown as OembedVideoData);
    })();
  }, [url]);

  if (!oembedData) {
    return (
      <LoaderWrapper>
        <FAIcon icon={faSpinner} spin size="2x" />
      </LoaderWrapper>
    );
  }

  return <IframeWrapper className={className} html={oembedData.html} />;
})``;
