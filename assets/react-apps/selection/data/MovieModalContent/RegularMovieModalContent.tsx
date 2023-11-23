import styled from "styled-components";
import { Movie, Stylable } from "../../types";
import { MovieAdditionalData } from "./types";
import {
  InlineHeading,
  MovieHeader,
  MovieImageWrapper,
  Paragraph,
} from "./commons";
import Info from "./Info";
import { DangerousHTML, Divider } from "../../App/commons";
import TrailerEmbed from "./TrailerEmbed";
import ScreeningsByVenue from "./ScreeningsByVenue";
import MovieLinks from "./MovieLinks";

export default styled(function RegularMovieModalContent({
  className,
  movie,
  movieData,
}: Stylable & { movie: Movie; movieData: MovieAdditionalData }) {
  return (
    <div className={className}>
      <MovieHeader>
        <MovieImageWrapper>
          <DangerousHTML html={movieData.image} />

          <MovieLinks movieData={movieData} />
        </MovieImageWrapper>
        <Info movieData={movieData} />
      </MovieHeader>

      <Divider />

      {movieData.synopsis && (
        <Paragraph>
          <InlineHeading>Synopsis</InlineHeading>
          <DangerousHTML
            html={movieData.synopsis}
            cssStyle="display: inline;"
          />
        </Paragraph>
      )}

      {movieData.comments && (
        <Paragraph cssStyle="font-style: italic;">
          {movieData.comments}
        </Paragraph>
      )}

      <Divider />

      <ScreeningsByVenue movie={movie} movieData={movieData} />

      {movieData.trailerUrl && <TrailerEmbed url={movieData.trailerUrl} />}
    </div>
  );
})`
  > ${Divider} {
    margin-top: 20px;
    margin-bottom: 20px;
  }

  > ${Paragraph} + ${Paragraph} {
    margin-top: 20px;
  }

  > ${ScreeningsByVenue} {
    margin-bottom: 40px;
  }
`;
