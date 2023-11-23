import styled from "styled-components";
import {
  Movie,
  Screening,
  Stylable,
  isScreeningAlwaysAvailable,
  isStreamingScreening,
} from "../../../types";
import SingleScreening from "./SingleScreening";
import { ReactNode, useContext } from "react";
import { MovieAdditionalData } from "../types";
import StreamingButton from "./StreamingButton";
import { isDateBetween } from "../../../../helpers";
import Editions, { SingleEdition } from "../../../Editions";
import { DataContext } from "../../DataProvider";
import { INLINE_HEADING_COLOR, InlineHeading } from "../commons";

const LINK_ONLY_DURING_FESTIVAL =
  "⚠️ El link se habilitará sólo en las fechas del festival";

const LINK_ONLY_ON_SCHEDULES_DATES =
  "⚠️ El link se habilitará sólo en las fechas de proyección estipuladas.";

const LINK_UNAVAILABLE = "⚠️ Streaming no disponible";

const Title = styled.span`
  font-weight: 600;
`;

function anyScreeningsLeftForStreamingMovies(
  currentEdition: SingleEdition,
  screenings: Screening[]
): {
  isDisabled: boolean;
  disabledReason?: ReactNode;
} {
  if (screenings.some((screening) => !isStreamingScreening(screening))) {
    throw new Error(
      'Do not call "anyScreeningsLeftForStreamingMovies" on non-streaming screenings.'
    );
  }

  if (isScreeningAlwaysAvailable(screenings[0])) {
    const from = Editions.from(currentEdition);
    const to = Editions.to(currentEdition);

    if (!from || !to) {
      throw new Error(
        `From and to dates for edition ${currentEdition.number} should be defined at this point.`
      );
    }

    if (isDateBetween(new Date(), from, to)) {
      return { isDisabled: false };
    }

    return {
      isDisabled: true,
      disabledReason: LINK_ONLY_DURING_FESTIVAL,
    };
  }

  const lastScreening = screenings[screenings.length - 1];

  if (isScreeningAlwaysAvailable(lastScreening)) {
    throw new Error(
      "Since we have a screening that is not always available (the first one), there cannot be other screenings that are always available."
    );
  }

  if (
    isDateBetween(
      new Date(),
      new Date(screenings[0].isoDate),
      new Date(lastScreening.isoDate)
    )
  ) {
    return { isDisabled: false };
  }

  return {
    isDisabled: true,
    disabledReason: LINK_ONLY_ON_SCHEDULES_DATES,
  };
}

const StreamingButtonWrapper = styled.div`
  display: flex;
  flex-flow: column;
  align-items: center;

  > span {
    max-width: 200px;
    text-align: center;
    margin-top: 10px;
    color: gray;
  }
`;

export default styled(function ScreeningsGroup({
  className,
  title,
  movie,
  movieData,
  screenings,
}: Stylable & {
  title: ReactNode;
  movie: Movie;
  movieData: MovieAdditionalData;

  /**
   * All of these screenings are expected to share the venue.
   *
   * This is why if one of them has `alwaysAvailable` enabled, it must be the only screening
   * in the array. Otherwise, it doesn't make sense.
   */
  screenings: Screening[];
}) {
  const { currentEdition } = useContext(DataContext);

  if (screenings.length === 0) {
    return null;
  }

  return (
    <fieldset className={className}>
      <legend>
        <InlineHeading>Proyecciones</InlineHeading>
        <Title>{title}</Title>
      </legend>

      {((): ReactNode => {
        if (isScreeningAlwaysAvailable(screenings[0])) {
          if (movieData.streamingLink) {
            const { isDisabled, disabledReason } =
              anyScreeningsLeftForStreamingMovies(currentEdition, screenings);

            return (
              <StreamingButtonWrapper>
                <StreamingButton
                  isDisabled={isDisabled}
                  streamingUrl={movieData.streamingLink}
                />
                {disabledReason && <span>{disabledReason}</span>}
              </StreamingButtonWrapper>
            );
          }

          return (
            <StreamingButtonWrapper>
              <StreamingButton isDisabled />
              <span>{LINK_UNAVAILABLE}</span>
            </StreamingButtonWrapper>
          );
        }

        return (
          <>
            {screenings.map((screening) => (
              <SingleScreening
                key={`${movie.id}_${screening.raw}`}
                screening={{ ...screening, movie }}
              />
            ))}

            {((): ReactNode => {
              if (!isStreamingScreening(screenings[0])) {
                return null;
              }

              /**
               * If one of the screenings is a streaming screening, then all share the same
               * `movieData.streamingLink`
               */

              if (movieData.streamingLink) {
                const { isDisabled, disabledReason } =
                  anyScreeningsLeftForStreamingMovies(
                    currentEdition,
                    screenings
                  );

                return (
                  <StreamingButtonWrapper>
                    <StreamingButton
                      isDisabled={isDisabled}
                      streamingUrl={movieData.streamingLink}
                    />
                    {disabledReason && <span>{disabledReason}</span>}
                  </StreamingButtonWrapper>
                );
              }

              return (
                <StreamingButtonWrapper>
                  <StreamingButton isDisabled />
                  <span>{LINK_UNAVAILABLE}</span>
                </StreamingButtonWrapper>
              );
            })()}
          </>
        );
      })()}
    </fieldset>
  );
})`
  display: flex;
  gap: 35px;
  justify-content: center;

  font-family: "Open Sans"; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  border-width: 1px;
  border-radius: 10px;
  border-color: ${INLINE_HEADING_COLOR};

  > legend {
    padding: 5px;
  }
`;
