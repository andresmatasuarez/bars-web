import styled from "styled-components";
import { Movie, Stylable } from "../../types";
import { MovieBlockAdditionalData } from "./types";
import { MovieHeader, MovieImageWrapper } from "./commons";
import Info from "./Info";
import { DangerousHTML, Divider } from "../../App/commons";
import MoviesCarousel from "./MoviesCarousel";
import ScreeningsByVenue from "./ScreeningsByVenue";

export default styled(function MovieBlockModalContent({
  className,
  movie,
  movieData,
}: Stylable & { movie: Movie; movieData: MovieBlockAdditionalData }) {
  return (
    <div className={className}>
      <MovieHeader>
        <MovieImageWrapper>
          <DangerousHTML html={movieData.image} />
        </MovieImageWrapper>

        <Info movieData={movieData} />
      </MovieHeader>

      <Divider />

      <MoviesCarousel movies={movieData.movies} />

      <Divider />

      <ScreeningsByVenue movie={movie} movieData={movieData} />
    </div>
  );
})`
  > ${Divider} {
    margin-top: 20px;
    margin-bottom: 20px;
  }
`;
