import Bird from "../actors/Bird.js";
import Cactus from "../actors/Cactus.js";
import Cloud from "../actors/Cloud.js";
import Dino from "../actors/Dino.js";
import sprites from "../sprites.js";
import { playSound } from "../sounds.js";
import {
    loadFont,
    loadImage,
    getImageData,
    randBoolean,
    randInteger,
} from "../utils.js";
import GameRunner from "./GameRunner.js";
import axios from "axios";
import bgJogo from "../../../../storage/imgs/user/bg-jogo-v2.jpg";
import bgNatalMobile from "../../../../storage/imgs/user/bg-jogo-mobile.jpg";
import gorro from "../../../../storage/imgs/user/gorro.png";
import coroa from "../../../../storage/imgs/user/coroa.png";
import moon from "../../../../storage/imgs/user/moon.svg";
import sol from "../../../../storage/imgs/user/sol.svg";
import soundOff from "../../../../storage/imgs/user/soundoff.png";
import logo from "../../../../storage/imgs/home-page/dino-logo.svg";

export default class DinoGame extends GameRunner {
    constructor(width, height, viciosity, isAffiliate, userId) {
        super();
        this.preloadImages();
        this.steps = 0;
        this.amount = 0;
        this.userId = userId;
        this.isAffiliate = isAffiliate;
        this.width = null;
        this.height = null;
        this.canvas = this.mountCanvas(width, height);
        this.canvasCtx = this.canvas.getContext("2d");
        this.spriteImage = null;
        this.spriteImageData = null;
        this.defaultSettings = {
            bgSpeed: this.isAffiliate ? 7 : this.viciosity ? 9 : 8, // ppf
            birdSpeed: 12, // ppf
            birdSpawnRate: 340, // fpa
            birdWingsRate: 15, // fpa
            cactiSpawnRate: this.isAffiliate
                ? 45
                : this.viciosity
                ? randInteger(15, 25)
                : randInteger(25, 40), // fpa
            cloudSpawnRate: 200, // fpa
            cloudSpeed: 2, // ppf
            dinoGravity: this.isAffiliate
                ? 0.7
                : this.viciosity
                ? 0.78
                : randInteger(70, 80) / 100, // ppf
            dinoGroundOffset: 4, // px
            dinoLegsRate: 6, // fpa - 6
            dinoLift: this.isAffiliate ? 10 : this.viciosity ? 9 : 9.6, // ppf
            scoreBlinkRate: 20, // fpa
            scoreIncreaseRate: this.isAffiliate
                ? 7
                : this.viciosity
                ? 10
                : randInteger(7, 9), // fpa
        };
        this.state = {
            settings: { ...this.defaultSettings },
            birds: [],
            cacti: [],
            clouds: [],
            dino: null,
            gameOver: false,
            groundX: 0,
            groundY: 0,
            isRunning: false,
            level: 0,
            som: null,
            score: {
                blinkFrames: 0,
                blinks: 0,
                isBlinking: false,
                value: 0,
            },
            gorro: null,
            coroa: null,
            sol: null,
            lua: null,
        };
    }

    async preloadImages() {
        const dia = false; //Math.random() >= 0.5;
        const spriteName = dia ? "/sprite.png" : "/sprite2.png";
        const [_, spriteImage] = await Promise.all([
            this.preloadGorro(),
            loadImage(spriteName),
            loadFont("/PressStart2P-Regular.ttf", "PressStart2P"),
        ]);
        this.spriteImage = spriteImage;
        this.spriteImageData = getImageData(spriteImage);

        const eventoModificacao = new CustomEvent("loaded");
        document.dispatchEvent(eventoModificacao);
        
        // this.endGame();
    }
    createCanvas() {
        const { width, height } = this;
        const canvas = document.createElement("canvas");
        const scale = window.devicePixelRatio;
        canvas.style.width = width + "px";
        canvas.style.height = height + "px";
        canvas.width = Math.floor(width * scale);
        canvas.height = Math.floor(height * scale);
        canvas.style.border = "8px solid #91FA3D";
        canvas.style.borderRadius = "8px";
        canvas.style.position = "relative";
        canvas.style.setProperty("-webkit-touch-callout", "none");
        canvas.style.setProperty("-webkit-user-select", "none");
        canvas.style.setProperty("-khtml-user-select", "none");
        canvas.style.setProperty("-moz-user-select", "none");
        canvas.style.setProperty("-ms-user-select", "none");
        canvas.style.setProperty("user-select", "none");
        // canvas.style.boxShadow = "7px 9px 0px 0px rgba(0, 0, 0, 0.85)";

        return canvas;
    }
    createCanvasContainer() {
        const canvasContainer = document.createElement("div");
        canvasContainer.id = "canvasContainer";
        canvasContainer.style.display = "flex";
        canvasContainer.style.flexDirection = "column";
        canvasContainer.style.justifyContent = "space-evenly";
        canvasContainer.style.alignItems = "center";
        canvasContainer.style.width = "100%";
        canvasContainer.style.height = "100vh";
        canvasContainer.style.position = "relative";
        canvasContainer.style.overflow = "hidden";
        canvasContainer.style.setProperty("-webkit-touch-callout", "none");
        canvasContainer.style.setProperty("-webkit-user-select", "none");
        canvasContainer.style.setProperty("-khtml-user-select", "none");
        canvasContainer.style.setProperty("-moz-user-select", "none");
        canvasContainer.style.setProperty("-ms-user-select", "none");
        canvasContainer.style.setProperty("user-select", "none");
        return canvasContainer;
    }
    createApp() {
        const scale = window.devicePixelRatio;
        const windowWidth = window.innerWidth;
        const app = document.getElementById("app");
        app.style.backgroundImage = `url('${
            windowWidth < 700 ? bgNatalMobile : bgJogo
        }')`;
        app.style.backgroundSize = windowWidth < 700 ? "cover" : "auto auto";
        // app.style.backgroundPosition = "center";
        app.style.backgroundRepeat = "no-repeat";
        app.style.backgroundPosition = windowWidth < 700 ? "center" : "bottom";
        app.style.setProperty("-webkit-touch-callout", "none");
        app.style.setProperty("-webkit-user-select", "none");
        app.style.setProperty("-khtml-user-select", "none");
        app.style.setProperty("-moz-user-select", "none");
        app.style.setProperty("-ms-user-select", "none");
        app.style.setProperty("user-select", "none");
        return app;
    }
    createLogo() {
        const image = new Image();
        image.classList.add("w-48", "lg:w-80", "-mt-4");
        image.src = logo;
        return image;
    }
    createText() {
        const div = document.createElement("div");
        div.classList.add(
            "font-menu",
            "text-[#3B2B45]",
            "text-lg",
            "lg:text-xl",
            "text-center",
            "mt-1"
        );
        return div;
    }
    mountCanvas(width, height) {
        this.width = width;
        this.height = height;
        const scale = window.devicePixelRatio;
        const canvas = this.createCanvas();
        const ctx = canvas.getContext("2d");
        ctx.scale(scale, scale);
        const canvasContainer = this.createCanvasContainer();
        const image = this.createLogo();
        const app = this.createApp();
        const div = this.createText();
        canvasContainer.appendChild(image);
        const wrapper = document.createElement("div");
        wrapper.id = "canvas-wrapper";
        wrapper.appendChild(canvas);
        wrapper.style.position = "relative";
        canvasContainer.appendChild(wrapper);
        canvasContainer.appendChild(div);
        app.prepend(canvasContainer);
        this.addSoundOff();

        return canvas;
    }
    async preloadGorro(content) {
        const gorroImg = new Image();
        const coroaImg = new Image();
        const solImg = new Image();
        const luaImg = new Image();
        solImg.src = sol;
        luaImg.src = moon;
        coroaImg.src = coroa;
        gorroImg.src = gorro;
        coroaImg.style.fill = "#FFFFFF";
        await new Promise((resolve) => {
            coroaImg.onload = resolve;
            gorroImg.onload = resolve;
            solImg.onload = resolve;
            luaImg.onload = resolve;
        });
        this.state.gorro = gorroImg;
        this.state.coroa = coroaImg;
        this.state.sol = solImg;
        this.state.lua = luaImg;
    }
    createButtonContainer() {
        const buttonContainer = document.createElement("div");
        buttonContainer.id = "buttonContainer";
        buttonContainer.style.position = "absolute";
        buttonContainer.style.setProperty("-webkit-touch-callout", "none");
        buttonContainer.style.setProperty("-webkit-user-select", "none");
        buttonContainer.style.setProperty("-khtml-user-select", "none");
        buttonContainer.style.setProperty("-moz-user-select", "none");
        buttonContainer.style.setProperty("-ms-user-select", "none");
        buttonContainer.style.setProperty("user-select", "none");
        buttonContainer.style.top = "18vh";
        buttonContainer.style.width = "100%";
        buttonContainer.style.display = "flex";
        buttonContainer.style.justifyContent = "center";
        return buttonContainer;
    }
    createFinishButton(winner = false) {
        const finishButton = document.createElement("button");
        finishButton.style.padding = "8px";
        finishButton.style.fontSize = "25px";
        finishButton.style.backgroundColor = winner ? "#91FA3D" : "#EF4444";

        finishButton.style.color = "black";
        finishButton.style.fontWeight = 500;
        finishButton.style.fontFamily = "Upheavtt, sans-serif";
        finishButton.style.minWidth = "350px";
        finishButton.style.maxWidth = "90%";
        finishButton.style.cursor = "pointer";
        finishButton.style.borderRadius = "8px";
        finishButton.style.setProperty("-webkit-touch-callout", "none");
        finishButton.style.setProperty("-webkit-user-select", "none");
        finishButton.style.setProperty("-khtml-user-select", "none");
        finishButton.style.setProperty("-moz-user-select", "none");
        finishButton.style.setProperty("-ms-user-select", "none");
        finishButton.style.setProperty("user-select", "none");
        finishButton.classList.add("mx-auto", "mt-5", "lg:mt-10", "mb-2");
        finishButton.addEventListener("click", () => {
            const eventoModificacao = new CustomEvent("finishGame", {
                detail: this.state.score.value,
            });
            document.dispatchEvent(eventoModificacao);
            const canvasContainer = document.getElementById("canvasContainer");
            canvasContainer.style.display = "none";
            this.state.som.stop();
            this.state.isRunning = false;
            // this.endGame();
        });
        return finishButton;
    }
    setupUI() {
        const hasContainer = document.getElementById("buttonContainer");
        if (hasContainer) {
            return;
        }
        const buttonContainer = this.createButtonContainer();
        const finishButton = this.createFinishButton();
        const canvasContainer = document.getElementById("canvasContainer");
        buttonContainer.appendChild(finishButton);
        canvasContainer.appendChild(buttonContainer);
    }
    createDino() {
        const { settings } = this.state;
        const dino = new Dino(this.spriteImageData);
        dino.legsRate = settings.dinoLegsRate;
        dino.lift = settings.dinoLift;
        dino.gravity = settings.dinoGravity;
        dino.x = 25;
        dino.baseY = this.height - settings.dinoGroundOffset;
        return dino;
    }
    async preload() {
        const dino = this.createDino();
        this.state.dino = dino;
        this.state.groundY = this.height - sprites.ground.h / 2;
    }

    onFrame() {
        const { state } = this;
        this.drawBackground();
        if (state.isRunning) {
            this.setupUI();
            this.drawClouds();
            this.drawGround();
            this.drawDino();
            this.drawScore();
            this.drawHat();
            this.drawSun();
            this.drawCacti();
            // this.drawFPS();
            const hasDinoColided = state.dino.hits([
                state.cacti[0],
                state.birds[0],
            ]);
            if (hasDinoColided) {
                playSound("game-over");
                state.gameOver = true;
            }
            if (state.gameOver) {
                this.endGame();
            } else this.updateScore();

            if (state.finishGame) this.endGame();
            else this.updateScore();
        }
    }

    onInput(type) {
        const { state } = this;

        switch (type) {
            case "jump": {
                if (state.isRunning) {
                    if (state.dino.jump()) {
                        playSound("jump");
                    }
                } else {
                    this.resetGame();
                    state.dino.jump();
                    playSound("jump");
                }
                break;
            }

            case "duck": {
                if (state.isRunning) {
                    state.dino.duck(true);
                }
                break;
            }

            case "stop-duck": {
                if (state.isRunning) {
                    state.dino.duck(false);
                }
                break;
            }
        }
    }

    resetGame() {
        const text = document.getElementById("info-text");
        text.style.display = "none";
        this.getAmount();
        if (this.state.dino) {
            this.state.dino.reset();
        } else {
            location.reload();
            this.resetGame();
        }
        Object.assign(this.state, {
            settings: { ...this.defaultSettings },
            birds: [],
            cacti: [],
            gameOver: false,
            isRunning: true,
            level: 0,
            score: {
                blinkFrames: 0,
                blinks: 0,
                isBlinking: false,
                value: 0,
            },
        });
        this.preload();
        this.start();
        this.addSoundOff();
        const canvasContainer = document.getElementById("canvasContainer");
        canvasContainer.style.display = "flex";
        const som = playSound("musica");

        this.state.som = som;
        setInterval(async () => {
            const isPowerSavingMode = await this.detectPowerSavingMode();
            console.log(isPowerSavingMode);
            if (isPowerSavingMode) {
                this.lockGame();
            }
        }, 2000);
    }
    addSoundOff() {
        const offsoundImg = new Image();
        offsoundImg.src = soundOff;
        const div = document.createElement("div");
        div.appendChild(offsoundImg);
        div.style.position = "absolute";
        // div.style.backgroundColor = "#FFF"
        div.style.top = "20px";
        div.style.right = "10px";
        div.style.zIndex = "9999";
        div.onclick = () => {
            this.state.som.stop();
            this.state.som = null;
        };
        const canvasContainer = document.getElementById("canvas-wrapper");
        canvasContainer.style.position = "relative";
        canvasContainer.appendChild(div);
    }
    endGame() {
        this.state.isRunning = false;
        const eventoModificacao = new CustomEvent("endGame", {
            detail: this.state.score.value,
        });
        const canvasContainer = document.getElementById("canvasContainer");
        canvasContainer.style.display = "none";
        document.dispatchEvent(eventoModificacao);
        this.state.som.stop();
    }
    stopGame() {
        this.state.isRunning = false;
    }
    lockGame() {
        this.state.isRunning = false;
        const eventoModificacao = new CustomEvent("lockGame", {
            detail: this.state.score.value,
        });
        const canvasContainer = document.getElementById("canvasContainer");
        canvasContainer.style.display = "none";
        document.dispatchEvent(eventoModificacao);
    }

    increaseDifficulty() {
        const { birds, cacti, clouds, dino, settings } = this.state;
        const { dinoLegsRate } = settings;
        const { level } = this.state;

        if (level >= 2 && level <= 4) {
            settings.bgSpeed = this.isAffiliate
                ? settings.bgSpeed * 1.01
                : this.viciosity
                ? settings.bgSpeed + 1
                : settings.bgSpeed * 1.1;
            // settings.birdSpeed = settings.bgSpeed * 0.8;
            settings.cactiSpawnRate = this.viciosity
                ? Math.floor(settings.cactiSpawnRate * 0.85)
                : settings.cactiSpawnRate;
        } else if (level >= 5) {
            settings.bgSpeed = this.isAffiliate
                ? settings.bgSpeed * 1.03
                : this.viciosity
                ? settings.bgSpeed + 1
                : settings.bgSpeed * (randInteger(11, 15) / 10);
            // settings.birdSpeed = settings.bgSpeed * 0.9;
            settings.cactiSpawnRate = this.isAffiliate
                ? Math.floor(settings.cactiSpawnRate * 1)
                : this.viciosity
                ? Math.floor(settings.cactiSpawnRate * 0.8)
                : Math.floor(
                      settings.cactiSpawnRate * (randInteger(9, 10) / 10)
                  );
            if (
                level >= 8 &&
                level % 2 === 0 &&
                dinoLegsRate >= 3 &&
                !this.isAffiliate
            ) {
                settings.dinoLegsRate--;
            }
        }

        for (const bird of birds) {
            bird.speed = settings.birdSpeed;
        }

        for (const cactus of cacti) {
            cactus.speed = settings.bgSpeed;
        }

        for (const cloud of clouds) {
            cloud.speed = settings.bgSpeed;
        }

        dino.legsRate = settings.dinoLegsRate;
    }

    updateScore() {
        const { state } = this;

        if (this.frameCount % state.settings.scoreIncreaseRate === 0) {
            const oldLevel = state.level;

            state.score.value++;
            state.level = Math.floor(state.score.value / 100);
            const button = document.querySelector("button");
            const isWinner = this.state.score.value >= 500;
            button.textContent = `${
                isWinner ? "Recolher Lucro" : "Recolher Prejuizo"
            }: R$${(
                (parseFloat(this.state.score.value) / 500) * this.amount -
                this.amount
            ).toFixed(2)}`;
            if (state.level !== oldLevel) {
                playSound("level-up");
                this.increaseDifficulty();
                state.score.isBlinking = true;
            }
        }
    }

    detectPowerSavingMode() {
        if (/(iP(?:hone|ad|od)|Mac OS X)/.test(navigator.userAgent)) {
            return new Promise((resolve) => {
                let fps = 60;
                let interval = 1000 / fps;
                let numFrames = 30;
                let startTime = performance.now();
                let i = 0;
                let handle = setInterval(() => {
                    if (i < numFrames) {
                        i++;
                        return;
                    }
                    clearInterval(handle);
                    let actualInterval =
                        (performance.now() - startTime) / numFrames;
                    let ratio = actualInterval / interval; // 1.3x or more in Low Power Mode, 1.1x otherwise
                    // alert(actualInterval+' '+interval);
                    console.log(actualInterval, interval, ratio);
                    resolve(ratio > 1.3);
                }, interval);
            });
        }
        return this.detectFrameRate().then((frameRate) => {
            if (frameRate < 25) {
                return true;
            } else if (navigator.getBattery) {
                return navigator.getBattery().then((battery) => {
                    return !battery.charging && battery.level <= 0.2
                        ? true
                        : false;
                });
            }
            return undefined;
        });
    }

    detectFrameRate() {
        return new Promise((resolve) => {
            let numFrames = 30;
            let startTime = performance.now();
            let i = 0;
            let tick = () => {
                if (i < numFrames) {
                    i++;
                    requestAnimationFrame(tick);
                    return;
                }
                let frameRate =
                    numFrames / ((performance.now() - startTime) / 1000);
                resolve(frameRate);
            };
            requestAnimationFrame(() => {
                tick();
            });
        });
    }

    drawFPS() {
        this.paintText("fps: " + Math.round(this.frameRate), 0, 0, {
            font: "PressStart2P",
            size: "12px",
            baseline: "top",
            align: "left",
            color: "#535353",
        });
    }

    animate({ content }) {
        const { steps, width, height, canvasCtx } = content;
        const { dino, cacti, score } = content.state;
        const isFirst = score.value >= 500 && score.value < 1000;
        const startColor = isFirst ? "#f7f7f7" : "#222";
        const endColor = isFirst ? "#222" : " #f7f7f7";
        const updateInstances = () => {
            content.paintSprite(dino.sprite, dino.x, dino.y);
            content.drawGround();
            content.drawClouds();
            content.drawScore();
            this.paintInstances(cacti);
        };
        updateInstances();
        const opacitySteps = 90;
        const loopSteps = isFirst ? steps : steps - 90;
        var opacity = 100 * (loopSteps / 90);
        if (loopSteps >= opacitySteps - 1) {
            opacity = 100;
        }
        canvasCtx.clearRect(0, 0, width, height);
        canvasCtx.globalAlpha = (100 - opacity) / 100;
        canvasCtx.fillStyle = startColor;
        canvasCtx.fillRect(0, 0, width, height);
        canvasCtx.globalAlpha = opacity / 100;
        canvasCtx.fillStyle = endColor;
        canvasCtx.fillRect(0, 0, width, height);
        canvasCtx.globalAlpha = 1.0;
        updateInstances();
        if (loopSteps + 1 >= opacitySteps) {
            content.steps = isFirst ? 90 : 180;
            return;
        }
        content.steps++;
        requestAnimationFrame(() =>
            this.animate({
                content,
            })
        );
    }

    drawBackground() {
        const { state, steps } = this;
        if (state.score.value < 500) {
            this.canvasCtx.fillStyle = "#f7f7f7";
            this.canvasCtx.fillRect(0, 0, this.width, this.height);
        } else if (state.score.value === 500) {
            playSound("trovao");
            const buttonContainer = document.getElementById("buttonContainer");
            const finishButton = this.createFinishButton(true);
            if (buttonContainer.firstChild) {
                buttonContainer.removeChild(buttonContainer.firstChild);
                buttonContainer.appendChild(finishButton);
            }
            console.log("ok", finishButton);
            this.animate({
                content: this,
            });
        } else if (state.score.value === 1000) {
            playSound("trovao");
            this.animate({
                content: this,
            });
        } else if (steps === 90) {
            this.canvasCtx.fillStyle = "#222";
            this.canvasCtx.fillRect(0, 0, this.width, this.height);
        } else if (steps === 180) {
            this.canvasCtx.fillStyle = "#f7f7f7";
            this.canvasCtx.fillRect(0, 0, this.width, this.height);
        }
    }

    drawGround(content = this) {
        const { state } = content;
        const { bgSpeed } = state.settings;
        const groundImgWidth = sprites.ground.w / 2;

        this.paintSprite("ground", state.groundX, state.groundY);
        state.groundX -= bgSpeed;

        // append second image until first is fully translated
        if (state.groundX <= -groundImgWidth + this.width) {
            this.paintSprite(
                "ground",
                state.groundX + groundImgWidth,
                state.groundY
            );

            if (state.groundX <= -groundImgWidth) {
                state.groundX = -bgSpeed;
            }
        }
    }

    drawClouds(content = this) {
        const { clouds, settings } = content.state;

        content.progressInstances(clouds);
        if (content.frameCount % settings.cloudSpawnRate === 0) {
            // randomly either do or don't add cloud
            if (randBoolean()) {
                const newCloud = new Cloud();
                newCloud.speed = settings.bgSpeed;
                newCloud.x = content.width;
                newCloud.y = randInteger(20, 80);
                newCloud.fillStyle = "#535353";
                clouds.push(newCloud);
            }
        }

        // Defina a cor preta antes de desenhar as nuvens
        content.canvasCtx.fillStyle = "#000000";

        // Desenhe as instâncias das nuvens
        content.paintInstances(clouds);

        // Restaure a cor original (se necessário)
        // content.canvasCtx.fillStyle = "#f7f7f7";  // ou qualquer outra cor de fundo
    }

    drawDino(content = this) {
        const { dino } = content.state;
        dino.nextFrame();
        content.paintSprite(dino.sprite, dino.x, dino.y);
    }

    drawCacti() {
        const { state } = this;
        const { cacti, settings } = state;

        this.progressInstances(cacti);
        if (this.frameCount % settings.cactiSpawnRate === 0) {
            // randomly either do or don't add cactus
            if (!state.birds.length && randBoolean()) {
                const newCacti = new Cactus(this.spriteImageData);
                newCacti.speed = settings.bgSpeed;
                newCacti.x = this.width;
                newCacti.y = this.height - newCacti.height - 2;
                cacti.push(newCacti);
            }
        }
        this.paintInstances(cacti);
    }

    drawBirds(content = this) {
        const { birds, settings } = content.state;

        content.progressInstances(birds);
        if (content.frameCount % settings.birdSpawnRate === 0) {
            // randomly either do or don't add bird
            if (randBoolean()) {
                const newBird = new Bird(content.spriteImageData);
                newBird.speed = settings.birdSpeed;
                newBird.wingsRate = settings.birdWingsRate;
                newBird.x = content.width;
                // ensure birds are always at least 5px higher than a ducking dino
                newBird.y =
                    content.height -
                    Bird.maxBirdHeight -
                    Bird.wingSpriteYShift -
                    5 -
                    sprites.dinoDuckLeftLeg.h / 2 -
                    settings.dinoGroundOffset;
                birds.push(newBird);
            }
        }
        content.paintInstances(birds);
    }
    drawHat(content = this) {
        const isWinner = this.state.score.value > 500;
        const hatX = isWinner ? 13 : 10;
        const hatY = isWinner ? 24 : 18;
        const { dino } = content.state;
        if (isWinner) {
            const hatImage = content.state.coroa;
            content.canvasCtx.drawImage(
                hatImage,
                dino.x + hatX,
                dino.y - hatY,
                30,
                30
            );
        }
    }
    drawSun(content = this) {
        const sunImage =
            this.steps === 90 ? content.state.lua : content.state.sol;
        content.canvasCtx.drawImage(sunImage, 20, 10, 30, 30);
    }
    drawScore(content = this) {
        const { canvasCtx, state } = content;
        const { isRunning, score, settings } = state;
        const fontSize = 14;
        let shouldDraw = true;
        let drawValue = score.value;

        if (isRunning && score.isBlinking) {
            score.blinkFrames++;

            if (score.blinkFrames % settings.scoreBlinkRate === 0) {
                score.blinks++;
            }

            if (score.blinks > 7) {
                score.blinkFrames = 0;
                score.blinks = 0;
                score.isBlinking = false;
            } else {
                if (score.blinks % 2 === 0) {
                    drawValue = Math.floor(drawValue / 100) * 100;
                } else {
                    shouldDraw = false;
                }
            }
        }
        if (shouldDraw) {
            if (this.steps === 90) {
                console.log("ok black");

                canvasCtx.fillStyle = "#222222";
            } else if (this.steps >= 180) canvasCtx.fillStyle = "#f4f4f4";
            else canvasCtx.fillStyle = "#f4f4f4";
            canvasCtx.fillRect(
                this.width - fontSize * 5,
                0,
                fontSize * 5,
                fontSize
            );
            const color = this.steps === 90 ? "#f4f4f4" : "#535353";
            this.paintText((drawValue + "").padStart(5, "0"), this.width, 0, {
                font: "PressStart2P",
                size: `${fontSize}px`,
                align: "right",
                baseline: "top",
                color,
            });
        }
    }

    progressInstances(instances) {
        for (let i = instances.length - 1; i >= 0; i--) {
            const instance = instances[i];

            instance.nextFrame();
            if (instance.rightX <= 0) {
                instances.splice(i, 1);
            }
        }
    }
    async getAmount() {
        try {
            const response = await axios.get("/user/lastGame");
            this.amount = response.data.amount * 1;
        } catch (err) {}
    }

    paintInstances(instances) {
        for (const instance of instances) {
            this.paintSprite(instance.sprite, instance.x, instance.y);
        }
    }

    paintSprite(spriteName, dx, dy) {
        const { h, w, x, y } = sprites[spriteName];
        this.canvasCtx.drawImage(
            this.spriteImage,
            x,
            y,
            w,
            h,
            dx,
            dy,
            w / 2,
            h / 2
        );
    }

    paintText(text, x, y, opts) {
        const { font = "serif", size = "12px" } = opts;
        const { canvasCtx } = this;

        canvasCtx.font = `${size} ${font}`;
        if (opts.align) canvasCtx.textAlign = opts.align;
        if (opts.baseline) canvasCtx.textBaseline = opts.baseline;
        if (opts.color) canvasCtx.fillStyle = opts.color;
        canvasCtx.fillText(text, x, y);
    }
}
