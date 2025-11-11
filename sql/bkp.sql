--
-- PostgreSQL database dump
--

\restrict SeJEmdg6yrFQcEVEguVOToD3ecKx8WGk0CBxJNcfWo7KMFsuRDMHT6mGfcRTc88

-- Dumped from database version 16.10
-- Dumped by pg_dump version 16.10

-- Started on 2025-11-04 08:12:22

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
-- TOC entry 4934 (class 0 OID 0)
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
-- TOC entry 4935 (class 0 OID 0)
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
-- TOC entry 4936 (class 0 OID 0)
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
-- TOC entry 4937 (class 0 OID 0)
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
-- TOC entry 4938 (class 0 OID 0)
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
-- TOC entry 4939 (class 0 OID 0)
-- Dependencies: 216
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4755 (class 2604 OID 16642)
-- Name: ads id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ads ALTER COLUMN id SET DEFAULT nextval('public.ads_id_seq'::regclass);


--
-- TOC entry 4753 (class 2604 OID 16622)
-- Name: event_participants id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants ALTER COLUMN id SET DEFAULT nextval('public.event_participants_id_seq'::regclass);


--
-- TOC entry 4748 (class 2604 OID 16604)
-- Name: events id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events ALTER COLUMN id SET DEFAULT nextval('public.events_id_seq'::regclass);


--
-- TOC entry 4760 (class 2604 OID 16690)
-- Name: global_config id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.global_config ALTER COLUMN id SET DEFAULT nextval('public.global_config_id_seq'::regclass);


--
-- TOC entry 4745 (class 2604 OID 16590)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 4780 (class 2606 OID 16648)
-- Name: ads ads_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ads
    ADD CONSTRAINT ads_pkey PRIMARY KEY (id);


--
-- TOC entry 4776 (class 2606 OID 16625)
-- Name: event_participants event_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_pkey PRIMARY KEY (id);


--
-- TOC entry 4778 (class 2606 OID 16627)
-- Name: event_participants event_participants_user_id_event_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_user_id_event_id_key UNIQUE (user_id, event_id);


--
-- TOC entry 4774 (class 2606 OID 16612)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- TOC entry 4782 (class 2606 OID 16701)
-- Name: global_config global_config_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.global_config
    ADD CONSTRAINT global_config_pkey PRIMARY KEY (id);


--
-- TOC entry 4770 (class 2606 OID 16597)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4772 (class 2606 OID 16599)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 4784 (class 2606 OID 16633)
-- Name: event_participants event_participants_event_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_event_id_fkey FOREIGN KEY (event_id) REFERENCES public.events(id) ON DELETE CASCADE;


--
-- TOC entry 4785 (class 2606 OID 16628)
-- Name: event_participants event_participants_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.event_participants
    ADD CONSTRAINT event_participants_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 4783 (class 2606 OID 16613)
-- Name: events events_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


-- Completed on 2025-11-04 08:12:22

--
-- PostgreSQL database dump complete
--

\unrestrict SeJEmdg6yrFQcEVEguVOToD3ecKx8WGk0CBxJNcfWo7KMFsuRDMHT6mGfcRTc88

