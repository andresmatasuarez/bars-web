import styled from "styled-components";
import { useEffect, useState } from "react";
import { Movie, Stylable } from "../../types";
import {
  MovieAdditionalData,
  MovieBlockAdditionalData,
  isMovieBlockAdditionalData,
} from "./types";
import { ModalHeader, ModalLoading, modalContentStyles } from "../MovieModal";
import MovieBlockModalContent from "./MovieBlockModalContent";
import RegularMovieModalContent from "./RegularMovieModalContent";

export default styled(function MovieModalContent({
  className,
  movie,
  onClose,
}: Stylable & {
  movie: Movie;
  onClose: () => void;
}) {
  const [movieData, setMovieData] = useState<
    MovieAdditionalData | MovieBlockAdditionalData | null
  >(null);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    setIsLoading(true);
    (async () => {
      const response = await fetch(movie.permalink);
      const fetchedMovieData = (await response.json()) as
        | MovieAdditionalData
        | MovieBlockAdditionalData;
      setMovieData(fetchedMovieData);
      setIsLoading(false);
    })();
  }, [movie.permalink]);

  if (isLoading || !movieData) {
    return <ModalLoading cssStyle="background: black" />;
  }

  return (
    <div className={className}>
      <ModalHeader onClose={onClose} />

      {isMovieBlockAdditionalData(movieData) ? (
        <MovieBlockModalContent movie={movie} movieData={movieData} />
      ) : (
        <RegularMovieModalContent movie={movie} movieData={movieData} />
      )}
    </div>
  );
})`
  ${modalContentStyles}

  background: black;
  padding: 0 20px 40px 20px;

  color: white;
  font-family: "Oswald"; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  > ${ModalHeader} {
    margin-top: 10px;
  }
`;
