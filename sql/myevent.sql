--
-- PostgreSQL database dump
--

\restrict wHbvgBQCQZgVuYBCmBgwdWpO2072ClFBCZwFiL8i7hz1Z8oGrBgjxcOlL8lOOza

-- Dumped from database version 16.10
-- Dumped by pg_dump version 16.10

-- Started on 2025-11-11 10:26:45

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16649)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 4945 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 16639)
-- Name: ads; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ads (
    id integer NOT NULL,
    image character varying(255) NOT NULL,
    link character varying(255),
    active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT now(),
    title character varying(255) DEFAULT ''::character varying,
    display_time integer DEFAULT 5
);


ALTER TABLE public.ads OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16638)
-- Name: ads_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ads_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ads_id_seq OWNER TO postgres;

--
-- TOC entry 4946 (class 0 OID 0)
-- Dependencies: 222
-- Name: ads_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ads_id_seq OWNED BY public.ads.id;


--
-- TOC entry 221 (class 1259 OID 16619)
-- Name: event_participants; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.event_participants (
    id integer NOT NULL,
    user_id integer NOT NULL,
    event_id integer NOT NULL,
    subscribed_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.event_participants OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16618)
-- Name: event_participants_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.event_participants_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.event_participants_id_seq OWNER TO postgres;

--
-- TOC entry 4947 (class 0 OID 0)
-- Dependencies: 220
-- Name: event_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.event_participants_id_seq OWNED BY public.event_participants.id;


--
-- TOC entry 219 (class 1259 OID 16601)
-- Name: events; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.events (
    id integer NOT NULL,
    name character varying(150) NOT NULL,
    summary text,
    image character varying(255),
    address character varying(255),
    city character varying(100),
    date_start timestamp without time zone,
    date_end timestamp without time zone,
    latitude numeric(10,8),
    longitude numeric(11,8),
    capacity integer DEFAULT 100,
    unlimited boolean DEFAULT false,
    cost character varying(50) DEFAULT 'Gratuito'::character varying,
    created_by integer,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.events OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16600)
-- Name: events_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.events_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.events_id_seq OWNER TO postgres;

--
-- TOC entry 4948 (class 0 OID 0)
-- Dependencies: 218
-- Name: events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.events_id_seq OWNED BY public.events.id;


--
-- TOC entry 225 (class 1259 OID 16687)
-- Name: global_config; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.global_config (
    id integer NOT NULL,
    site_title character varying(255) DEFAULT 'Hotsite Evento'::character varying,
    page_title character varying(255) DEFAULT 'Hotsite Evento'::character varying,
    logo_path character varying(255) DEFAULT 'assets/img/logo.png'::character varying,
    favicon_path character varying(255) DEFAULT 'assets/img/favicon.png'::character varying,
    theme_light jsonb DEFAULT '{"text": "#000000", "primary": "#0d6efd", "secondary": "#6c757d", "background": "#ffffff"}'::jsonb,
    theme_dark jsonb DEFAULT '{"text": "#ffffff", "primary": "#0d6efd", "secondary": "#6c757d", "background": "#121212"}'::jsonb,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.global_config OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16686)
-- Name: global_config_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.global_config_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.global_config_id_seq OWNER TO postgres;

--
-- TOC entry 4949 (class 0 OID 0)
-- Dependencies: 224
-- Name: global_config_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.global_config_id_seq OWNED BY public.global_config.id;


--
-- TOC entry 217 (class 1259 OID 16587)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password_hash character varying(255) NOT NULL,
    name character varying(100),
    email character varying(100),
    type text DEFAULT 'participant'::text NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    active boolean DEFAULT true,
    reset_token text,
    CONSTRAINT users_type_check CHECK ((type = ANY (ARRAY['admin'::text, 'participant'::text])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16586)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 4950 (class 0 OID 0)
-- Dependencies: 216
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4756 (class 2604 OID 16642)
-- Name: ads id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ads ALTER COLUMN id SET DEFAULT nextval('public.ads_id_seq'::regclass);


--
-- TOC entry 4754 (class 2604 OID 16622)
-- Name: event_participants id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants ALTER COLUMN id SET DEFAULT nextval('public.event_participants_id_seq'::regclass);


--
-- TOC entry 4749 (class 2604 OID 16604)
-- Name: events id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events ALTER COLUMN id SET DEFAULT nextval('public.events_id_seq'::regclass);


--
-- TOC entry 4761 (class 2604 OID 16690)
-- Name: global_config id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.global_config ALTER COLUMN id SET DEFAULT nextval('public.global_config_id_seq'::regclass);


--
-- TOC entry 4745 (class 2604 OID 16590)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 4937 (class 0 OID 16639)
-- Dependencies: 223
-- Data for Name: ads; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ads (id, image, link, active, created_at, title, display_time) FROM stdin;
5	assets/uploads/ad_1762569643.png	sdfjvndlkfv	t	2025-11-07 23:24:07.593337	teste 3	5
4	assets/uploads/ad_1762569657.jpeg	xfçvmlçkmb	t	2025-11-07 23:16:03.221702	teste 2	5
3	assets/uploads/ad_1762569666.png	teste.com	t	2025-11-07 20:46:41.982657	Teste 1	5
1	assets/uploads/ad_1762569676.png	https://www.exemplo1.com	t	2025-11-01 14:09:43.204916	Padrão 1	5
2	assets/uploads/ad_1762569700.jpeg	https://www.exemplo2.com	t	2025-11-01 14:09:43.204916	Padrão 2	5
\.


--
-- TOC entry 4935 (class 0 OID 16619)
-- Dependencies: 221
-- Data for Name: event_participants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.event_participants (id, user_id, event_id, subscribed_at) FROM stdin;
7	1	1	2025-11-01 15:44:44.425283
9	2	1	2025-11-01 15:55:26.229397
\.


--
-- TOC entry 4933 (class 0 OID 16601)
-- Dependencies: 219
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.events (id, name, summary, image, address, city, date_start, date_end, latitude, longitude, capacity, unlimited, cost, created_by, created_at) FROM stdin;
1	Hackathon Tech	Maratona de programação e inovação tecnológica.	assets/uploads/sample1.jpg	Av. Paulista, São Paulo	São Paulo	2025-11-05 00:00:00	2025-11-07 00:00:00	-23.56168400	-46.65598100	200	f	Gratuito	1	2025-11-01 14:09:43.204916
2	Workshop de IA	Treinamento intensivo sobre aplicações práticas de IA.	assets/uploads/sample2.jpg	Rua das Flores, Curitiba	Curitiba	2025-11-10 00:00:00	2025-11-10 00:00:00	-25.42840000	-49.27330000	100	f	R$ 50,00	1	2025-11-01 14:09:43.204916
3	Feira de Startups atualizado	Evento de networking e exposição de startups.		Centro de Convenções, Recife	Recife	2025-12-01 00:00:00	2025-12-02 00:00:00	-8.04760000	-34.87700000	500	f	Gratuito	1	2025-11-01 14:09:43.204916
\.


--
-- TOC entry 4939 (class 0 OID 16687)
-- Dependencies: 225
-- Data for Name: global_config; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.global_config (id, site_title, page_title, logo_path, favicon_path, theme_light, theme_dark, created_at) FROM stdin;
\.


--
-- TOC entry 4931 (class 0 OID 16587)
-- Dependencies: 217
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, username, password_hash, name, email, type, created_at, active, reset_token) FROM stdin;
1	admin	$2a$06$HEuHlnkA8moUHGIsLjFTEeuJQpMBVicHqTIo9zSTN4Gdmfg4v4WFi	Administrador teste	admin@teste.com	admin	2025-11-01 14:09:43.204916	t	\N
2	gabriel	$2a$06$hzvhsrIY.lL8HaFOH7buF.y/iX0JFdZq956TZiilj8ZrOujWZxGP6	Gabriel Participante	gabriel@teste.com	admin	2025-11-01 14:09:43.204916	t	\N
3	teste	$2y$10$ITZwGFGN2WK4EbLR1wPrq.kTm88Nwenb8LaKAnqa2zlXNNuZzwSPW	Teste da silva	teste@teste.com	participant	2025-11-06 16:18:14.875804	t	\N
\.


--
-- TOC entry 4951 (class 0 OID 0)
-- Dependencies: 222
-- Name: ads_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ads_id_seq', 5, true);


--
-- TOC entry 4952 (class 0 OID 0)
-- Dependencies: 220
-- Name: event_participants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.event_participants_id_seq', 10, true);


--
-- TOC entry 4953 (class 0 OID 0)
-- Dependencies: 218
-- Name: events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.events_id_seq', 4, true);


--
-- TOC entry 4954 (class 0 OID 0)
-- Dependencies: 224
-- Name: global_config_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.global_config_id_seq', 1, false);


--
-- TOC entry 4955 (class 0 OID 0)
-- Dependencies: 216
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- TOC entry 4781 (class 2606 OID 16648)
-- Name: ads ads_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ads
    ADD CONSTRAINT ads_pkey PRIMARY KEY (id);


--
-- TOC entry 4777 (class 2606 OID 16625)
-- Name: event_participants event_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_pkey PRIMARY KEY (id);


--
-- TOC entry 4779 (class 2606 OID 16627)
-- Name: event_participants event_participants_user_id_event_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_user_id_event_id_key UNIQUE (user_id, event_id);


--
-- TOC entry 4775 (class 2606 OID 16612)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- TOC entry 4783 (class 2606 OID 16701)
-- Name: global_config global_config_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.global_config
    ADD CONSTRAINT global_config_pkey PRIMARY KEY (id);


--
-- TOC entry 4771 (class 2606 OID 16597)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4773 (class 2606 OID 16599)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 4785 (class 2606 OID 16633)
-- Name: event_participants event_participants_event_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_event_id_fkey FOREIGN KEY (event_id) REFERENCES public.events(id) ON DELETE CASCADE;


--
-- TOC entry 4786 (class 2606 OID 16628)
-- Name: event_participants event_participants_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 4784 (class 2606 OID 16613)
-- Name: events events_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


-- Completed on 2025-11-11 10:26:45

--
-- PostgreSQL database dump complete
--

\unrestrict wHbvgBQCQZgVuYBCmBgwdWpO2072ClFBCZwFiL8i7hz1Z8oGrBgjxcOlL8lOOza

